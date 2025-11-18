#!/usr/bin/env python3
# -*- coding: utf8 -*-
#
#    Copyright 2018 Daniel Perron
#
#    Base on Mario Gomez <mario.gomez@teubi.co>   MFRC522-Python
#
#    This file use part of MFRC522-Python
#    MFRC522-Python is a simple Python implementation for
#    the MFRC522 NFC Card Reader for the Raspberry Pi.
#
#    DoorSytem is an implementation of MFRC-Python modified
#    to use spidev instead of spi
#    to be able to use python3
#
#    MFRC522-Python is free software:
#    you can redistribute it and/or modify
#    it under the terms of
#    the GNU Lesser General Public License as published by the
#    Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    MFRC522-Python is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Lesser General Public License for more details.
#
#    You should have received a copy of the
#    GNU Lesser General Public License along with MFRC522-Python.
#    If not, see <http://www.gnu.org/licenses/>.
#

import RPi.GPIO as GPIO
import MFRC522
import pymysql
import signal
import datetime
import time
import sys

ACTION_UNKNOWN = 0
ACTION_ACCEPTED = 1
ACTION_BAD_CARD = 2
ACTION_EXPIRED = 3
ACTION_WRONG_LEVEL = 4
ACTION_INVALID = 5


# reader identification

readerName = "reader 1"
readerID = "0"
readerZone = 0
# my sql info
# my sql info (Docker / Laravel)
mysqlHost = "db"          # nom du service MariaDB dans docker-compose
mysqlDatabase = "rfid"    # DB définie dans docker-compose
mysqlUserName = "rfid"    # user défini dans docker-compose
mysqlPassword = "rfid"    # password défini dans docker-compose

# relay definition

RELAY_PIN = 12
RELAY_ON = 0
RELAY_OFF = 1

relay_time = time.time()
relay_status = False


# function to set relay
def setRelay(value):
    global relay_status
    global relay_time
    if value:
        relay_status = True
        relay_time = time.time()
    else:
        relay_status = False
    GPIO.output(RELAY_PIN, RELAY_ON if relay_status else RELAY_OFF)


# LED DEFINITION

LED_R_PIN = 20
LED_G_PIN = 16
LED_B_PIN = 21
LED_ON = 1
LED_OFF = 0


# set GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.output(RELAY_PIN, RELAY_OFF)
GPIO.setup(LED_R_PIN, GPIO.OUT)
GPIO.setup(LED_G_PIN, GPIO.OUT)
GPIO.setup(LED_B_PIN, GPIO.OUT)


led_status = False
led_time = time.time()
# FUNCTION TO SET


def setLed(R, G, B):
    global led_time
    global led_status
    GPIO.output(LED_R_PIN, LED_ON if R > 0 else LED_OFF)
    GPIO.output(LED_G_PIN, LED_ON if G > 0 else LED_OFF)
    GPIO.output(LED_B_PIN, LED_ON if B > 0 else LED_OFF)
    led_status = (R+G+B) > 0
    if led_status:
        led_time = time.time()
#        print("ledstatus = {}".format(led_status))

setLed(0, 0, 0)
continue_reading = True


# Capture SIGINT for cleanup when the script is aborted
def end_read(signal, frame):
    global continue_reading
    print("Ctrl+C captured, ending read.")
    continue_reading = False
    setRelay(False)
    setLed(0, 0, 0)
    GPIO.cleanup()

# Hook the SIGINT
signal.signal(signal.SIGINT, end_read)

# Create an object of the class MFRC522
MIFAREReader = MFRC522.MFRC522()

# Welcome message
print("Door System using Raspberry Pi")
print("Press Ctrl-C to stop.")

# open an sql session
try:

    sql_con = pymysql.connect(host=mysqlHost, user=mysqlUserName,
                              passwd=mysqlPassword, db=mysqlDatabase)

    cur = sql_con.cursor()

except pymysql.err.OperationalError:
    print("unable to connect to DataBase")
    quit()


# function to read uid an conver it to a string
def uidToString(uid):
    mystring = ""
    for i in uid:
        mystring = format(i, '02X') + mystring
    return mystring

# function to read a card and if a new one is in then report it
# otherwise report NULL
# If no card is read after .5 sec clear the old  card info

current_card = None
last_time = time.time()


def readCard():
    global current_card
    global last_time
    # Scan for cards
    (status, TagType) = MIFAREReader.MFRC522_Request(MIFAREReader.PICC_REQIDL)

    # If a card is found
    if status == MIFAREReader.MI_OK:
        #      print ("Card detected")
        # Get the UID of the card
        (status, uid) = MIFAREReader.MFRC522_SelectTagSN()
        # If we have the UID, continue
        if status == MIFAREReader.MI_OK:
            # print("current_card {}".format(current_card))
            # print("uid {}".format(uid))
            last_time = time.time()  # bounce delay to detect no card
            if current_card == uid:
                return None  # same card
            else:
                # print("Card read UID:", uid)
                current_card = uid
                # return a  string of the key
                return uidToString(uid)
        # else:
        # print("Authentication error")
    else:
        # no card
        if abs(time.time() - last_time) > 0.5:
            # print("no card")
            current_card = None
    # if we are here we didn't detect anything
    return None

#########################
# Validate reader
#  check if reader is allowed
#
#  return True if it is allowed
#
#  else False
#


def validateReader(sqlcursor):
    # plus de gestion des lecteurs en base, on accepte toujours
    return True


###########################
# Check Card
#
# function to check  the RFID card from  the sql database
#
# return true if valid otherwise false

def validateCard(sqlcursor, serial_id):
    """
    Vérifie si le badge existe, trouve l'utilisateur lié,
    et enregistre une ligne dans la table 'heures'.
    """

    # on attend un numéro de badge en base 16 -> int
    try:
        badge_num = int(serial_id, 16)
    except ValueError:
        print(f"UID invalide : {serial_id}")
        return False

    # 1) vérifier que le lecteur est valide (même si on renvoie toujours True pour l'instant)
    if not validateReader(sqlcursor):
        print("Lecteur non autorisé")
        return False

    # 2) récupérer l'utilisateur lié au badge
    sql_select_user = """
        SELECT u.id
        FROM utilisateurs u
        JOIN badges b ON b.id = u.badge_id
        WHERE b.numero = %s
        LIMIT 1
    """
    count = sqlcursor.execute(sql_select_user, (badge_num,))

    if count == 0:
        print(f"Badge inconnu : {serial_id} (numero={badge_num})")
        return False

    user_id = sqlcursor.fetchone()[0]

    # 3) insérer dans 'heures'
    # entree_sortie = True -> entrée (pour l'instant on ne gère pas la sortie)
    now = datetime.datetime.now()

    sql_insert_heure = """
        INSERT INTO heures (entree_sortie, heure, utilisateur_id, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s)
    """
    sqlcursor.execute(sql_insert_heure, (True, now, user_id, now, now))

    print(f"Heure enregistrée pour utilisateur {user_id} à {now}")
    return True

while continue_reading:
    card_read = readCard()

    if card_read is not None:
        setLed(1, 0, 0)
        if validateCard(cur, card_read):
            setLed(0, 1, 0)
            setRelay(True)
        else:
            setRelay(False)
            setLed(1, 0, 0)
        sql_con.commit()

    # check led turn if off after 5 sec
    if led_status:
        if abs(time.time() - led_time) > 5:
            setLed(0, 0, 0)

    # check for relay turn it off after 5 sec
    if relay_status:
        if abs(time.time() - relay_time) > 5:
            setRelay(False)

