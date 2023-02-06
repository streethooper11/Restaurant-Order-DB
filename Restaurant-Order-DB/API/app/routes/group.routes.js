
module.exports = app => {
    const group = require("../controllers/group.controller.js");

    var router = require("express").Router();

    // Creating new group
    router.post("/", group.create);

    // Adding EmailID to group
    router.put("/:Group_ID", group.update);

    // Getting groups by GroupID
    router.get("/:Group_ID", group.findOne);

    app.use('/api/group', router);


};
