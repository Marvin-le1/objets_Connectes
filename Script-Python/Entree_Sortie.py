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
#    DoorSystem is an implementation of MFRC-Python modified
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

# --- MySQL info : script sur le Pi, DB dans Docker ---
mysqlHost = "127.0.0.1"
mysqlPort = 3307
mysqlDatabase = "rfid"
mysqlUserName = "rfid"
mysqlPassword = "rfid"

# --- Relay definition ---
RELAY_PIN = 12
RELAY_ON = 0
RELAY_OFF = 1

relay_time = time.time()
relay_status = False


def setRelay(value: bool) -> None:
    """Activer / désactiver le relais."""
    global relay_status, relay_time
    if value:
        relay_status = True
        relay_time = time.time()
    else:
        relay_status = False
    GPIO.output(RELAY_PIN, RELAY_ON if relay_status else RELAY_OFF)


# --- LED definition ---
LED_R_PIN = 20
LED_G_PIN = 16
LED_B_PIN = 21
LED_ON = 1
LED_OFF = 0

GPIO.setmode(GPIO.BCM)
GPIO.setup(RELAY_PIN, GPIO.OUT)
GPIO.output(RELAY_PIN, RELAY_OFF)
GPIO.setup(LED_R_PIN, GPIO.OUT)
GPIO.setup(LED_G_PIN, GPIO.OUT)
GPIO.setup(LED_B_PIN, GPIO.OUT)

led_status = False
led_time = time.time()


def setLed(R: int, G: int, B: int) -> None:
    """Mettre à jour l'état des 3 LEDs."""
    global led_time, led_status
    GPIO.output(LED_R_PIN, LED_ON if R > 0 else LED_OFF)
    GPIO.output(LED_G_PIN, LED_ON if G > 0 else LED_OFF)
    GPIO.output(LED_B_PIN, LED_ON if B > 0 else LED_OFF)
    led_status = (R + G + B) > 0
    if led_status:
        led_time = time.time()


setLed(0, 0, 0)
continue_reading = True


def end_read(sig, frame):
    """Handler Ctrl+C : on arrête proprement."""
    global continue_reading
    print("Ctrl+C captured, ending read.")
    continue_reading = False
    setRelay(False)
    setLed(0, 0, 0)
    GPIO.cleanup()


signal.signal(signal.SIGINT, end_read)

# Create an object of the class MFRC522
MIFAREReader = MFRC522.MFRC522()

print("Door System using Raspberry Pi")
print("Press Ctrl-C to stop.")

# --- Connexion SQL ---
try:
    sql_con = pymysql.connect(
        host=mysqlHost,
        user=mysqlUserName,
        passwd=mysqlPassword,
        db=mysqlDatabase,
        port=mysqlPort,
    )
    cur = sql_con.cursor()
except pymysql.err.OperationalError:
    print("unable to connect to DataBase")
    sys.exit(1)


# --- Utils RFID ---

def uidToString(uid):
    """Convertit un UID (liste d'octets) en string hex (ex: 'A1B2C3D4')."""
    mystring = ""
    for i in uid:
        mystring = format(i, "02X") + mystring
    return mystring


current_card = None
last_time = time.time()


def readCard():
    """Lit une carte, renvoie l'UID string si nouvelle carte, sinon None."""
    global current_card, last_time

    (status, TagType) = MIFAREReader.MFRC522_Request(MIFAREReader.PICC_REQIDL)

    if status == MIFAREReader.MI_OK:
        (status, uid) = MIFAREReader.MFRC522_SelectTagSN()
        if status == MIFAREReader.MI_OK:
            last_time = time.time()
            if current_card == uid:
                return None
            current_card = uid
            return uidToString(uid)
    else:
        if abs(time.time() - last_time) > 0.5:
            current_card = None

    return None


# --- Reader / Badge logic ---

def validateReader(sqlcursor):
    """On ne gère pas les lecteurs en base dans ce projet -> toujours True."""
    return True


def validateCard(sqlcursor, serial_id: str) -> bool:
    """
    Vérifie si le badge existe, trouve l'utilisateur lié,
    et enregistre une ligne dans la table 'heures'.
    """

    # serial_id est un UID en hex (ex: 'A1B2C3D4'), on le convertit en int
    try:
        badge_num = int(serial_id, 16)
    except ValueError:
        print(f"UID invalide : {serial_id}")
        return False

    if not validateReader(sqlcursor):
        print("Lecteur non autorisé")
        return False

    # 1) récupérer l'utilisateur lié au badge
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

    # 2) insérer dans 'heures'
    now = datetime.datetime.now()
    sql_insert_heure = """
        INSERT INTO heures (entree_sortie, heure, utilisateur_id, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s)
    """
    sqlcursor.execute(sql_insert_heure, (True, now, user_id, now, now))

    print(f"Heure enregistrée pour utilisateur {user_id} à {now}")
    return True


# --- Main loop ---

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

    # éteindre la LED après 5s
    if led_status and abs(time.time() - led_time) > 5:
        setLed(0, 0, 0)

    # couper le relais après 5s
    if relay_status and abs(time.time() - relay_time) > 5:
        setRelay(False)