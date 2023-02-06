
module.exports = app => {
    const dish = require("../controllers/dish.controller.js");

    var router = require("express").Router();

    // Retrieve dish by menu and restaurant ID
    router.get("/:RestaurantID/", dish.findOne);

    // Retrieve amount of ingrdients needed based on Dish ID
    router.get("/amount/:Dish_ID/", dish.specialGet);

    app.use('/api/dish', router);
};
