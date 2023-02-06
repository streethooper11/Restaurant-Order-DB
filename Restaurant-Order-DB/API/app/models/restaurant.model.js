
const sql = require("./db.js");

// Constructor
const Restaurant = function(restaurant) {
    this.AdminEmail_ID = restaurant.AdminEmail_ID;
    this.Location = restaurant.Location;
    this.Name = restaurant.Name;
    this.R_ID = restaurant.R_ID;
};

// Creating new restaurant
Restaurant.create = (newRestaurant, result) => {
    sql.query("INSERT INTO restaurant SET ?", newRestaurant, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created restaurant: ", {...newRestaurant});
        result(null, {...newRestaurant} );
    });
};

// Getting all restaurants by name
Restaurant.getAll = (name, result) => {
    let query = "SELECT Name From restaurant";
    sql.query(query, (err, res) => {
        if (err) {
            console.log("error ", err);
            result(null, err);
            resturn;
        }
        console.log("restaurant: ", res);
        result(null, res);
    });
};

module.exports = Restaurant;
