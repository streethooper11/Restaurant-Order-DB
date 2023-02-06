const Group = require("../models/group.model.js");


// Create new group
exports.create = (req, res) => {
    // Validate request
    if (!req.body) {
        res.status(400).send({
            message: "Need group id!"
        });
    }
    
    // Create group
    const group = new Group({
        Email_ID: req.body.Email_ID,
        Group_ID: req.body.Group_ID
    });

    // Save group in database
    Group.create(group, (err, data) => {
        if (err)
            res.status(500).send({
                mesasge: 
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
    Group.updateByGroup_ID(
        req.params.Group_ID,
        new Group(req.body),
        (err, data) => {
            if (err) {
                if (err.kind === "not_found") {
                    res.status(400).send({
                        message: `Not found Group with group id ${req.params.Group_ID}.`
                    });
                } else {
                    res.status(500).send({
                        message: "Error udpating Group with id " + req.params.Group_ID
                    });
                }
            } else res.send(data);
        }
    );
};


// Retrieve info from group by GroupID
exports.findOne = (req, res) => {
    Group.findByGroupID(req.params.Group_ID, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    message: `Not found Group with id ${req.params.Group_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Group with group id: " + req.params.Group_ID
                });
            }
        } else res.send(data);
    });
};
