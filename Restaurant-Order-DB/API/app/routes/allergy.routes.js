
module.exports = app => {
    const allergy = require("../controllers/allergy.controller.js");

    var router = require("express").Router();

    // Creating new allergy
    router.post("/", allergy.create);

    // Getting all allergies by EmailID
    router.get("/:Email_ID", allergy.findOne);

    app.use('/api/allergy', router);
};
