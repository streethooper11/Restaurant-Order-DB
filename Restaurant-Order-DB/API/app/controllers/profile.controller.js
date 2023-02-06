const Profile = require("../models/profile.model.js");

//  Retrieve info from profile by Email_ID
exports.findOne = (req, res) => {
    Profile.findByEmailID(req.params.Email_ID, (err, data) => {
        if (err) {
            if (err.kind === "not_found") {
                res.status(404).send({
                    message: `Not found Profile with id ${req.params.Email_ID}.`
                });
            } else {
                res.status(500).send({
                    message: "Error retrieving Profile with email id: " + req.params.Email_ID
                });
            } 
        } else res.send(data);
    });
};
