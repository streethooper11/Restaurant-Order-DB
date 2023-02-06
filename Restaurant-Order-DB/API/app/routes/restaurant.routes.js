
module.exports = app => {
    const restaurant = require("../controllers/restaurant.controller.js");
    
    var router = require("express").Router();

    // Create a new Restaurant
    router.post("/", restaurant.create);

    // Retrieve all restaurants
    router.get("/", restaurant.findAll);

    app.use('/api/restaurant', router);
};
