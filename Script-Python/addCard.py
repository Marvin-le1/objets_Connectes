import pymysql
import datetime

# Demander les infos
serial_no = input("RFID card serial number ? (hex) ")
user_id = input("ID de l'utilisateur (utilisateurs.id) ? ")

# mettre en majuscules
serial_no = serial_no.upper()

# convertir l'UID hex en entier pour coller avec badges.numero
try:
    numero_badge = int(serial_no, 16)
except ValueError:
    print(f"UID invalide : {serial_no}")
    exit(1)

# ouvrir une session SQL vers la BDD Docker
try:
    sql_con = pymysql.connect(
        host="127.0.0.1",
        user="rfid",
        passwd="rfid",
        db="rfid",
        port=3307,
    )
    sqlcursor = sql_con.cursor()
except pymysql.err.OperationalError as e:
    print("Unable to connect to DataBase:", e)
    exit(1)

# 1) vérifier si le badge existe déjà
sql_request = "SELECT id, numero FROM badges WHERE numero = %s"
count = sqlcursor.execute(sql_request, (numero_badge,))

if count > 0:
    print(f"Error! RFID card {serial_no} (numero={numero_badge}) already in database")
    print(sqlcursor.fetchone())
else:
    # 2) insérer dans badges
    now = datetime.datetime.now()
    sql_insert_badge = """
        INSERT INTO badges (numero, created_at, updated_at)
        VALUES (%s, %s, %s)
    """
    sqlcursor.execute(sql_insert_badge, (numero_badge, now, now))
    badge_id = sql_con.insert_id()

    # 3) lier le badge à l'utilisateur
    sql_update_user = """
        UPDATE utilisateurs
        SET badge_id = %s, updated_at = %s
        WHERE id = %s
    """
    sqlcursor.execute(sql_update_user, (badge_id, now, user_id))

    # 4) commit + vérif
    sql_con.commit()

    if sqlcursor.rowcount > 0:
        print(f"RFID card {serial_no} (numero={numero_badge}) linked to user {user_id}")
    else:
        print(f"Badge créé (id={badge_id}) mais aucun utilisateur mis à jour (id={user_id} inexistant ?)")

sql_con.close()