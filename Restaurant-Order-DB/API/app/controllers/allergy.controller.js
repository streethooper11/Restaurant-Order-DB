const Allergy = require("../models/allergy.model.js");

// Create and save new allergy 
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create account
    const allergy = new Allergy({
        Email_ID: req.body.Email_ID,
        Allergy_Name: req.body.Allergy_Name,
        Name: req.body.Name,
    });

    // Save account in the database
    Allergy.create(allergy, (err, data) => {
        if (err)
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating a new allergy"
            });
        else res.send(data);
    });

};




// Get all allergies based on matching Email ID 
exports.findOne = (req, res) => {
    Allergy.findByID(req.params.Email_ID, (err, data) => {
        if (err) {
            if (err.kind == "not_found") {
                res.status(400).send({
                    message: `Not found restaurant with name ${req.params.Email_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Menu with name: " + req.params.Email_ID
                });
            }
        } else res.send(data);
    });
};
