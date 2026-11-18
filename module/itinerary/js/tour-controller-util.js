(function() {
    Array.prototype.remove = function(index) {
        this.splice(index, 1);
        return this;
    };
    Array.prototype.getLastIndex = function() {
        return this.length - 1;
    };
    Array.prototype.getLastItem = function() {
        return this[this.getLastIndex()];
    };
    window.averageGeoLocation = function(coords) {
        console.log(coords);
        if (!coords) return false;
        if (coords.length < 1) {
            return false;
        }
        if (!(Array.isArray(coords))) {
            return averageGeoLocation([coords]);
        }
        if (coords.length === 1) {
            var center = coords[0];
            if (center.map_lat) {
                center.map_lat = parseFloat(center.map_lat);
            }
            if (center.map_lng) {
                center.map_lng = parseFloat(center.map_lng);
            }
            return center;
        }

        let x = 0.0;
        let y = 0.0;
        let z = 0.0;

        for (let coord of coords) {
            let latitude = coord.map_lat * Math.PI / 180;
            let longitude = coord.map_lng * Math.PI / 180;

            x += Math.cos(latitude) * Math.cos(longitude);
            y += Math.cos(latitude) * Math.sin(longitude);
            z += Math.sin(latitude);
        }

        let total = coords.length;

        x = x / total;
        y = y / total;
        z = z / total;

        let centralLongitude = Math.atan2(y, x);
        let centralSquareRoot = Math.sqrt(x * x + y * y);
        let centralLatitude = Math.atan2(z, centralSquareRoot);

        return {
            map_lat: centralLatitude * 180 / Math.PI,
            map_lng: centralLongitude * 180 / Math.PI
        };
    };
    window.calcCrow = function(here, there) {
        var toRad = function(value) {
            return value * Math.PI / 180;
        }
        var R = 6371; // km
        var dLat = toRad(there.map_lat-here.map_lat);
        var dLon = toRad(there.map_lng-here.map_lng);
        var lat1 = toRad(here.map_lat);
        var lat2 = toRad(there.map_lat);

        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        var d = R * c;
        return d;
    };
    window.maxDistanceFromCenter = function(coords) {
        if (!coords) return 0;
        var center = averageGeoLocation(coords);
        var distances = [];
        coords.forEach(function(coord) {
            distances.push(calcCrow(center, coord));
        });
        distances.sort(function(val1, val2) {
            return val2 - val1;
        });
        return distances[0];
    };
    window.zoomLevelFromDistance = function(distance) {
        var zoom = Math.floor(Math.log2(80000/distance));
        if (isNaN(zoom) || zoom === Infinity) {
            return 12;
        }
    }
})();
