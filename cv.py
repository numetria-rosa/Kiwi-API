import json

# Load JSON data from file
with open('car.json', 'r') as file:
    records = json.load(file)

# Start with CREATE TABLE
sql_statements = """
CREATE TABLE IF NOT EXISTS carrier (
    pid INT AUTO_INCREMENT PRIMARY KEY,
    id VARCHAR(10),
    lcc INT,
    name VARCHAR(100),
    type VARCHAR(50)
);
"""

for data in records:
    # Escape single quotes in strings
    id_ = data["id"].replace("'", "''")
    name = data["name"].replace("'", "''")
    type_ = data["type"].replace("'", "''")
    lcc = 'NULL' if data["lcc"] is None else data["lcc"]

    sql_statements += f"""
INSERT INTO carrier (id, lcc, name, type)
VALUES ('{id_}', {lcc}, '{name}', '{type_}');
"""

# Save SQL file
with open('airlines.sql', 'w', encoding='utf-8') as sql_file:
    sql_file.write(sql_statements)

print("✅ SQL file 'airlines.sql' has been created.")
