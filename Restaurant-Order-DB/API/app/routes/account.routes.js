
module.exports = app => {
    const account = require("../controllers/account.controller.js");

    var router = require("express").Router();

    // Create a new account
    router.post("/", account.create);

    // Retrieve all accounts
    router.get("/", account.findAll);

    // Retrieve account by Email_ID
    router.get("/:Email_ID", account.findOne);

    // Delete account by Email_ID
    router.delete("/:Email_ID", account.remove);

    // Put request create new account  or update account if it exists
    router.put("/", account.createPut);




    app.use('/api/account', router);
};
