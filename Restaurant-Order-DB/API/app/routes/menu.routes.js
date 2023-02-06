
module.exports = app => {
    const menu = require("../controllers/menu.controller.js");

    var router = require("express").Router();

    // Getting all menus (test)
    router.get("/", menu.findAll);

    // Get Menu Name from Restaurant
    router.get("/:ID", menu.findOne);

    app.use('/api/menu', router);
};
