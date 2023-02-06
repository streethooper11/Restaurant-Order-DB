
const sql = require("./db.js");

// Constructor
const Account = function(account) {
    this.Email_ID = account.Email_ID;
    this.Password = account.Password;
    this.FName = account.FName;
    this.LName = account.LName;
    this.Type = account.Type;
};

// Creating new account
Account.create = (newAccount, result) => {
    sql.query("INSERT INTO Account SET ?", newAccount, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created account: ", { id: res.insertID, ...newAccount });
        result(null, { id: res.insertID, ...newAccount} );
    });
};

// Creating new account or updating it if it exists in database
Account.create = (newAccount, result) => {
    sql.query("REPLACE INTO Account SET ?", newAccount, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created account: ", { id: res.insertID, ...newAccount });
        result(null, { id: res.insertID, ...newAccount} );
    });
};




Account.getAll = (Email_ID, result) => {
    let query = "SELECT * FROM account";
    if (Email_ID) {
        query += ` WHERE EmailID LIKE '${Email_ID}'`;
    }

    sql.query(query, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(null, err);
            return;
    }
        console.log("account: ", res);
        result(null, res);
    });
};


Account.findByEmail_ID = (Email_ID, result) => {
    sql.query(`SELECT * FROM account WHERE Email_ID = '${Email_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found account: ", res[0]);
            result(null, res[0]);
            return;
        }
        // Not found Account with EmailID
        result({ kind: "not_found"}, null);

    });
};

Account.removeByEmail_ID = (Email_ID, result) => {
    sql.query(`DELETE FROM Account WHERE Email_ID = '${Email_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found account: ", res[0]);
            result(null, res[0]);
            return;
        }
        // Not found Account with EmailID
        result({ kind: "not_found"}, null);

    });
};



module.exports = Account;
