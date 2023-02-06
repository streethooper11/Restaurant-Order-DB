
const sql = require("./db.js");

// Constructor 
const Allergy = function(allergy) {
    this.Allergy_Name = allergy.Allergy_Name;
    this.Name = allergy.Name;
    this.Email_ID = allergy.Email_ID;
};

// Creating new account
Allergy.create = (newAllergy, result) => {
    sql.query("INSERT INTO Allergy SET ?", newAllergy, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        console.log("Created account: ", { id: res.insertID, ...newAllergy });
        result(null, { id: res.insertID, ...newAllergy} );
    });
};



// Getting all allergy 
Allergy.findByID = (Email_ID, result) => {
    sql.query(`SELECT * from 471db.Allergy WHERE Email_ID = '${Email_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        } 
        if (res.length) {
            console.log("Found menu: ", res);
            result(null, res);
            return;
        }
        // Not found Menu with Name
        result({ kind: "not_found"}, null);
    });
};

module.exports = Allergy;
