from flask import Flask, jsonify
import sqlite3

app = Flask(__name__)

def get_users():
    conn = sqlite3.connect("../database.db")
    cursor = conn.cursor()
    cursor.execute("SELECT id, name, email, age FROM Users")
    rows = cursor.fetchall()
    conn.close()
    return [{"id": r[0], "name": r[1], "email": r[2], "age": r[3]} for r in rows]

@app.route("/api/users")
def users():
    return jsonify(get_users())

if __name__ == "__main__":
    app.run(port=5000, debug=True)
