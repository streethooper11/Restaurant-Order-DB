
const sql = require("./db.js");

// Constructor
const Profile = function(profile) {
    this.Email_ID = profile.Email_ID;
    this.Order_History = profile.Order_History;
    this.Allergy = profile.Allergy;
    this.FName = profile.FName;
    this.LName = profile.LName;

};

// Get by Email ID 
Profile.findByEmailID = (Email_ID, result) => {
    sql.query(`SELECT * FROM profile WHERE Email_ID = '${Email_ID}'`, (err, res) => {
        if (err) {
            console.log("error: ", err);
            result(err, null);
            return;
        }
        if (res.length) {
            console.log("Found profile ", res[0]);
            result(null, res[0]);
            return;
        }
        // Not found Profile with Email_ID
        result({ kind: "not_found"}, null);
    });
};

module.exports = Profile;
