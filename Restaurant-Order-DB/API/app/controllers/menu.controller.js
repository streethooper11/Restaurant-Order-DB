const Menu = require("../models/menu.model.js");

exports.findAll = (req, res) => {
    Menu.getAll("", (err, data) => {
        res.send(data);
    });
};

// Get Menu based on matching Restaurant Name
exports.findOne = (req, res) => {
    Menu.findByID(req.params.ID, (err, data) => {
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
