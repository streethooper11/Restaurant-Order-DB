const Review = require("../models/review.model.js");

// Create and save new account
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create account
    const review = new Review({
        Rating: req.body.Rating,
        Comment: req.body.Comment,
        UserEmail_ID: req.body.UserEmail_ID,
        Date_Time: req.body.Date_Time,
        Reply: req.body.Reply,
        AdminEmail_ID: req.body.AdminEmail_ID,
        Order_ID: req.body.Order_ID
    });

    // Save account in the database
    Review.create(review, (err, data) => {
        if (err)
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating a new account"
            });
        else res.send(data);
    });

};



// Get Review based on matching Restaurant Name
exports.findOne = (req, res) => {
    Review.findByID(req.params.AdminEmail_ID, (err, data) => {
        if (err) {
            if (err.kind == "not_found") {
                res.status(400).send({
                    message: `Not found restaurant with email ${req.params.ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Review with name: " + req.params.ID
                });
            }
        } else res.send(data);
    });
};
