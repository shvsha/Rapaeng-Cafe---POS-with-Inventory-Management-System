function date_time() {
    try {
        var month_name = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var day_name = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

        var today = new Date();
        // set date if an id exists
        if (does_ID_Exist("date_today")) {
            document.getElementById('date_today').innerHTML = (day_name[today.getDay()]);
        }
        if (does_ID_Exist("full_date")) {
            document.getElementById('full_date').innerHTML = (month_name[today.getMonth()] + " " + today.getDate() + ", " + today.getFullYear()).toUpperCase();
        }
        if (does_ID_Exist("month_year")) {
            document.getElementById('month_year').innerHTML = (month_name[today.getMonth()] + " " + today.getFullYear()).toUpperCase();
        }
    } catch (error) {
        console.error("Error in date_time function: ", error);
    }
    // get the current time
    var hour = today.getHours();
    var min = today.getMinutes();
    var sec = today.getSeconds();
    var day = hour<12 ? "AM" : "PM";
    // format
    hour = hour<10 ? '0' + hour : hour;
    min = min<10 ? '0' + min : min;
    sec = sec<10 ? '0' + sec : sec;
} var inter = setInterval(date_time, 1000);

// Check if an element with the given ID exists
function does_ID_Exist(id) {
    return document.getElementById(id) !== null;
}