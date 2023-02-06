const Restaurant = require("../models/restaurant.model.js");

// Create and save new restaurant
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create restaurant
    const restaurant = new Restaurant({
        AdminEmail_ID:  req.body.AdminEmail_ID,
        R_ID: req.body.R_ID,
        Location: req.body.Location,
        Name: req.body.Name,
        Amount_Left: req.body.Amount_Left
    });

    // Saving restaurant in database
    Restaurant.create(restaurant, (err, data) => { 
        if (err)
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating new restaurant"
            });
        else res.send(data);
    });
};

// Retrieve all restaurant names from the database
exports.findAll = (req, res) => {
    const name = "";
    Restaurant.getAll(name, (err, data) => {
        if (err) 
            res.status(500).send({
                message: 
                err.message || "Some error occurred while retrieving restaurant names."
            });
        else res.send(data);
    });
};


