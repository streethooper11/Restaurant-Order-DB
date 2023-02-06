
module.exports = app => {
    const history = require("../controllers/history.controller.js");

    var router = require("express").Router();

    // Creating new group
    router.post("/", history.create);

    // Adding EmailID to group
    router.put("/:History_ID", history.update);

    // Getting groups by GroupID
    router.get("/:History_ID", history.findOne);

    app.use('/api/history', router);


};
