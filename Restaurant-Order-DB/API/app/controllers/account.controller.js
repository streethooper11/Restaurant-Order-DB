const Account = require("../models/account.model.js");

// Create and save new account
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create account
    const account = new Account({
        Email_ID: req.body.Email_ID,
        Password: req.body.Password,
        FName: req.body.FName,
        LName: req.body.LName,
        Type: req.body.Type
    });




    // Save account in the database
    Account.create(account, (err, data) => {
        if (err) 
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating a new account"
            });
        else res.send(data);
    });

};

exports.createPut = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }

    // Create account
    const account = new Account({
        Email_ID: req.body.Email_ID,
        Password: req.body.Password,
        FName: req.body.FName,
        LName: req.body.LName,
        Type: req.body.Type
    });




    // Save account in the database
    Account.create(account, (err, data) => {
        if (err) 
            res.status(500).send({
                message:
                err.message || "Some error occurred while creating a new account"
            });
        else res.send(data);
    });

};



// Retrieve all accounts from the database
exports.findAll = (req, res) => {
    const Email_ID = req.query.Email_ID;

    Account.getAll(Email_ID, (err, data) => {
        if (err)
            res.status(500).send({
                message: 
                err.message || "Some error occurred while retrieving accounts."
            });
        else res.send(data);
    });
};



// Retrieve account matching Email ID
exports.findOne = (req, res) => {
    Account.findByEmail_ID(req.params.Email_ID, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    message: `Not found Account with id ${req.params.Email_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Account with email id: " + req.params.Email_ID
                });
            }
        } else res.send(data);
    });
};

// Retrieve account matching Email ID
exports.remove = (req, res) => {
    Account.removeByEmail_ID(req.params.Email_ID, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    //message: `Not found Account with id ${req.params.Email_ID}.`
                    //mess
                    message: `Successfully deleted Account with id ${req.params.Email_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Account with email id: " + req.params.Email_ID
                });
            }
        } else res.send(data);
    });
};


