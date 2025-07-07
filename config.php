<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "athleto";

try {
    // 1. Connect to MySQL without selecting a database yet
    $pdo = new PDO("mysql:host=$host", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");

    // 3. Connect to the newly created (or existing) database
    $pdo->exec("USE $dbname");

    // 4. Define all table creation queries
    $tables = [

        "CREATE TABLE IF NOT EXISTS departments (
            dept_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            dept_name VARCHAR(50) NOT NULL UNIQUE
        )",

        "CREATE TABLE IF NOT EXISTS categories (
            category_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            category_name ENUM('Men', 'Women') NOT NULL UNIQUE
        )",

        "CREATE TABLE IF NOT EXISTS users (
            user_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            username VARCHAR(30) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(50) NOT NULL,
            role ENUM('admin', 'faculty', 'captain') NOT NULL,
            dept_id INT(5) NOT NULL,
            FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
        )",

        "CREATE TABLE IF NOT EXISTS athletes (
            athlete_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            category_id INT(5) NOT NULL,
            dept_id INT(5) NOT NULL,
            FOREIGN KEY (category_id) REFERENCES categories(category_id),
            FOREIGN KEY (dept_id) REFERENCES departments(dept_id)
        )",

        "CREATE TABLE IF NOT EXISTS events (
            event_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            event_name VARCHAR(50) NOT NULL,
            category_id INT(5) NOT NULL,
            is_relay BOOLEAN DEFAULT FALSE,
            FOREIGN KEY (category_id) REFERENCES categories(category_id)
        )",

        "CREATE TABLE IF NOT EXISTS participation (
            part_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            athlete_id INT(5) NOT NULL,
            event_id INT(5) NOT NULL,
            FOREIGN KEY (athlete_id) REFERENCES athletes(athlete_id),
            FOREIGN KEY (event_id) REFERENCES events(event_id)
        )",

        "CREATE TABLE IF NOT EXISTS relay_teams (
            team_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            dept_id INT(5) NOT NULL,
            event_id INT(5) NOT NULL,
            FOREIGN KEY (dept_id) REFERENCES departments(dept_id),
            FOREIGN KEY (event_id) REFERENCES events(event_id)
        )",

        "CREATE TABLE IF NOT EXISTS relay_team_members (
            member_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            team_id INT(5) NOT NULL,
            athlete_id INT(5) NOT NULL,
            FOREIGN KEY (team_id) REFERENCES relay_teams(team_id),
            FOREIGN KEY (athlete_id) REFERENCES athletes(athlete_id)
        )",

        "CREATE TABLE IF NOT EXISTS results (
            result_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            event_id INT(5) NOT NULL,
            athlete_id INT(5),
            relay_team_id INT(5),
            position INT(5) NOT NULL,
            added_by INT(5) NOT NULL,
            recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (event_id) REFERENCES events(event_id),
            FOREIGN KEY (athlete_id) REFERENCES athletes(athlete_id),
            FOREIGN KEY (relay_team_id) REFERENCES relay_teams(team_id),
            FOREIGN KEY (added_by) REFERENCES users(user_id)
        )",

        "CREATE TABLE IF NOT EXISTS certificate_templates (
            template_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            template_name VARCHAR(50) NOT NULL UNIQUE,
            file_path VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",

        "CREATE TABLE IF NOT EXISTS certificates (
            certificate_id INT(5) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            template_id INT(5) NOT NULL,
            result_id INT(5) NOT NULL,
            issued_by INT(5),
            issued_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (template_id) REFERENCES certificate_templates(template_id),
            FOREIGN KEY (result_id) REFERENCES results(result_id),
            FOREIGN KEY (issued_by) REFERENCES users(user_id)
        )"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
