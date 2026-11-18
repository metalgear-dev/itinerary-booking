
let checkReservationInterval = setInterval(checkReservationPassed, 10000);

let metaTag = document.querySelector("meta[name='itinerary-inquiry']");
let inquiry_id = metaTag ? metaTag.getAttribute("content") : "";

// global interval function
function checkReservationPassed(){
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            let data = JSON.parse(this.responseText);
            if (data.reservation_date === "true") {
                // self.setState({
                //   reservation_passed: true
                // });
                alert("予約時間を過ぎたため、診察室が開きました。");
                clearInterval(checkReservationInterval);
                window.location.reload();
            }
        }
    };
    xhr.open("POST", `https://tk2-237-28163.vs.sakura.ne.jp/api/getReservationDate`);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(JSON.stringify({inquiry_id : inquiry_id}));
}

readCookie = function(name){
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1,c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
            return c.substring(nameEQ.length,c.length);
        }
    }
    return null;
};