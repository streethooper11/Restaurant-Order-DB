const History = require("../models/history.model.js");


// Create new group
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Need group id!"
        });
    }
    
    // Create group
    const history = new History({
        UserEmail_ID: req.body.UserEmail_ID,
        Order_ID : req.body.Order_ID,
        Total_Price : req.body.Total_Price,
        Order_Place : req.body.Order_Place,
        History_ID : req.body.History_ID
    });

    // Save group in database
    History.create(history, (err, data) => {
        if (err)
            res.status(500).send({
                message: 
                err.message || "Some error occurred while creating a new group"
            });
        else res.send(data);
    });
};

// Updating group with groupID
exports.update = (req, res) => {
    // Validating request
    if (!req.body) {
        res.status(400).send({
            message: "Content cannot be empty!"
        });
    }
    console.log(req.body);
    History.updateByGroup_ID(
        req.params.History_ID,
        new History(req.body),
        (err, data) => {
            if (err) {
                if (err.kind === "not_found") {
                    res.status(400).send({
                        message: `Not found Group with group id ${req.params.History_ID}.`
                    });
                } else {
                    res.status(500).send({
                        message: "Error udpating Group with id " + req.params.Order_ID
                    });
                }
            } else res.send(data);
        }
    );
};


// Retrieve info from History by HistoryID 
exports.findOne = (req, res) => {
    History.findByGroupID(req.params.History_ID, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    message: `Not found History ID with id ${req.params.History_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Order History with History id: " + req.params.History_ID
                });
            }
        } else res.send(data);
    });
};
