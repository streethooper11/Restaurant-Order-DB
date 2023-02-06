
module.exports = app => {
    const order = require("../controllers/order.controller.js");

    var router = require("express").Router();

    // Create Order by Email ID
    router.post("/", order.create);

    // Get Menu Name from Restaurant
    router.get("/:ID", order.findOne);

    app.use('/api/order', router);
};
