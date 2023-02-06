
module.exports = app => {
    const profile = require("../controllers/profile.controller.js");

    var router = require("express").Router();

    // Getting Profile by EmailID
    router.get("/:Email_ID", profile.findOne);

    // Editing Profile
    //router.put("/:EmailID", profile.editOne);

    app.use('/api/profile', router);
};
