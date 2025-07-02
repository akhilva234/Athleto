<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "Athleto";

try {
    // 1. Connect to MySQL without selecting a database yet
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Database created or already exists.<br>";

    // 3. Connect to the newly created (or existing) database
    $pdo->exec("USE $dbname");

    // 4. Define all table creation queries
    $tables = [

        "CREATE TABLE IF NOT EXISTS Departments (
            dept_id INT(5) PRIMARY KEY,
            dept_name VARCHAR(50) NOT NULL UNIQUE
        )",

        "CREATE TABLE IF NOT EXISTS Categories (
            category_id INT(5) PRIMARY KEY,
            category_name ENUM('Men', 'Women') NOT NULL UNIQUE
        )",

        "CREATE TABLE IF NOT EXISTS Users (
            user_id INT(5) PRIMARY KEY,
            username VARCHAR(30) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'faculty', 'captain') NOT NULL,
            dept_id INT(5) NOT NULL,
            FOREIGN KEY (dept_id) REFERENCES Departments(dept_id)
        )",

        "CREATE TABLE IF NOT EXISTS Athletes (
            athlete_id INT(5) PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            category_id INT(5) NOT NULL,
            dept_id INT(5) NOT NULL,
            FOREIGN KEY (category_id) REFERENCES Categories(category_id),
            FOREIGN KEY (dept_id) REFERENCES Departments(dept_id)
        )",

        "CREATE TABLE IF NOT EXISTS Events (
            event_id INT(5) PRIMARY KEY,
            event_name VARCHAR(50) NOT NULL,
            category_id INT(5) NOT NULL,
            is_relay BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (category_id) REFERENCES Categories(category_id)
        )",

        "CREATE TABLE IF NOT EXISTS Participation (
            part_id INT(5) PRIMARY KEY,
            athlete_id INT(5) NOT NULL,
            event_id INT(5) NOT NULL,
            FOREIGN KEY (athlete_id) REFERENCES Athletes(athlete_id),
            FOREIGN KEY (event_id) REFERENCES Events(event_id)
        )",

        "CREATE TABLE IF NOT EXISTS Relay_teams (
            team_id INT(5) PRIMARY KEY,
            dept_id INT(5) NOT NULL,
            event_id INT(5) NOT NULL,
            FOREIGN KEY (dept_id) REFERENCES Departments(dept_id),
            FOREIGN KEY (event_id) REFERENCES Events(event_id)
        )",

        "CREATE TABLE IF NOT EXISTS Relay_team_members (
            member_id INT(5) PRIMARY KEY,
            team_id INT(5) NOT NULL,
            athlete_id INT(5) NOT NULL,
            FOREIGN KEY (team_id) REFERENCES Relay_teams(team_id),
            FOREIGN KEY (athlete_id) REFERENCES Athletes(athlete_id)
        )",

        "CREATE TABLE IF NOT EXISTS Results (
            result_id INT(5) PRIMARY KEY,
            event_id INT(5) NOT NULL,
            athlete_id INT(5),
            relay_team_id INT(5),
            position INT(5) NOT NULL,
            added_by INT(5) NOT NULL,
            recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES Events(event_id),
            FOREIGN KEY (athlete_id) REFERENCES Athletes(athlete_id),
            FOREIGN KEY (relay_team_id) REFERENCES Relay_teams(team_id),
            FOREIGN KEY (added_by) REFERENCES Users(user_id)
        )",

        "CREATE TABLE IF NOT EXISTS Certificate_templates (
            template_id INT(5) PRIMARY KEY,
            template_name VARCHAR(50) NOT NULL UNIQUE,
            file_path VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS Certificates (
            certificate_id INT(5) PRIMARY KEY,
            template_id INT(5) NOT NULL,
            result_id INT(5) NOT NULL,
            issued_by INT(5),
            issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (template_id) REFERENCES Certificate_templates(template_id),
            FOREIGN KEY (result_id) REFERENCES Results(result_id),
            FOREIGN KEY (issued_by) REFERENCES Users(user_id)
        )"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
        echo "Table created successfully.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
