
const mysql = require("mysql");
const dbConfig = require("../config/db.config.js");

// Creating a connection to the database
const connection = mysql.createConnection({
    host: dbConfig.HOST,
    user: dbConfig.USER,
    password: dbConfig.PASSWORD,
    database: dbConfig.DB
});

// Opening the MySQL Connection
connection.connect(error => {
    if (error) throw error;
    console.log("Successfully connected to the database.");
});

module.exports = connection;
