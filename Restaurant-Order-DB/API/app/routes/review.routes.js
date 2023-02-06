
module.exports = app => {
    const review = require("../controllers/review.controller.js");

    var router = require("express").Router();

    // Create review by restaurant email id
    router.post("/", review.create);

    // Get reviews from restaurant email id 
    router.get("/:AdminEmail_ID", review.findOne);

    app.use('/api/review', router);
};
