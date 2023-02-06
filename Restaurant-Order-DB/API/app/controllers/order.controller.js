const Order = require("../models/order.model.js");

// Create and save new account
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create account
    const order = new Order({
        Order_ID: req.body.Order_ID,
        Order_Time: req.body.Order_Time,
        Total_Price: req.body.Total_Price,
        Email_ID: req.body.Email_ID,
        Profile_Name: req.body.Profile_Name,
        RestaurantID: req.body.RestaurantID
    });

    // Save account in the database
    Order.create(order, (err, data) => {
        if (err)
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating a new account"
            });
        else res.send(data);
    });

};


// Get Order based on matching ID 
exports.findOne = (req, res) => {
    Order.findByID(req.params.ID, (err, data) => {
        if (err) {
            if (err.kind == "not_found") {
                res.status(400).send({
                    message: `Not found restaurant with name ${req.params.ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Menu with name: " + req.params.ID
                });
            }
        } else res.send(data);
    });
};
