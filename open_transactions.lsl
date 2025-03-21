//Auteur: Vlad Helsing of BloodMoon GRID
//V1 version du 21 mars 2025
// Script LSL : place this in a clickable object in-world

string WEB_URL = "https://bloodmoonpack.com/transactions/transactions_user.php"; // Change this to the location of the transactions.php file on your web server

default
{
    state_entry()
    {
        llSetText("Click to view your BM$ transactions", <0.5, 1.0, 0.5>, 1.0); // Change to your currency
    }

    touch_start(integer total_number)
    {
        key avatar = llDetectedKey(0);
        string url = WEB_URL + "?uuid=" + (string)avatar;
        llLoadURL(avatar, "View your transaction history", url);
    }
}
