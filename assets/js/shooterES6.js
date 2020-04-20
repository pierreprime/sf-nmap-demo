const CIRCLE = Math.PI * 2;
const STREET_WIDTH = 4;
const HOUSE_HEIGHT = 4;
const NMAP_URL = 'http://localhost:8000';

/**
 * @returns {*}
 */
function getHostsFromServer() {
    // fake it for now
    // var json = '{"id":"5d9c5742777d207de114c041","hosts":[{"id":"5d9c5742777d207de114c042","state":"up","addresses":[{"id":"5d9c5742777d207de114c043","address":"127.0.0.1","type":"ipv4","vendor":""}],"hostnames":[{"id":"5d9c5742777d207de114c044","name":"localhost","type":"user"},{"id":"5d9c5742777d207de114c045","name":"localhost","type":"PTR"}],"ports":[{"id":"5d9c5742777d207de114c046","number":80,"protocol":"tcp","state":"open","service":{"id":"5d9c5742777d207de114c047","name":"http","product":"","version":""}}]}]}';

    const json = '{"id":"5d9c8c02777d207de114c050","hosts":[{"id":"5d9c8c02777d207de114c085","state":"up","addresses":[{"id":"5d9c8c02777d207de114c086","address":"10.38.4.190","type":"ipv4","vendor":""}],"hostnames":[],"ports":[]},{"id":"5d9c8c02777d207de114c0a3","state":"up","addresses":[{"id":"5d9c8c02777d207de114c0a4","address":"10.38.4.210","type":"ipv4","vendor":""}],"hostnames":[],"ports":[]},{"id":"5d9c8c02777d207de114c0a5","state":"up","addresses":[{"id":"5d9c8c02777d207de114c0a6","address":"10.38.4.214","type":"ipv4","vendor":""}],"hostnames":[],"ports":[]},{"id":"5d9c8c02777d207de114c0a7","state":"up","addresses":[{"id":"5d9c8c02777d207de114c0a8","address":"10.38.4.216","type":"ipv4","vendor":""}],"hostnames":[],"ports":[]},{"id":"5d9c8c02777d207de114c0a9","state":"up","addresses":[{"id":"5d9c8c02777d207de114c0aa","address":"10.38.4.220","type":"ipv4","vendor":""}],"hostnames":[],"ports":[]},{"id":"5d9c8c02777d207de114c294","state":"up","addresses":[{"id":"5d9c8c02777d207de114c295","address":"10.38.7.254","type":"ipv4","vendor":""}],"hostnames":[{"id":"5d9c8c02777d207de114c296","name":"_gateway","type":"PTR"}],"ports":[{"id":"5d9c8c02777d207de114c297","number":53,"protocol":"tcp","state":"open","service":{"id":"5d9c8c02777d207de114c298","name":"domain","product":"","version":""}},{"id":"5d9c8c02777d207de114c299","number":67,"protocol":"tcp","state":"closed","service":{"id":"5d9c8c02777d207de114c29a","name":"dhcps","product":"","version":""}},{"id":"5d9c8c02777d207de114c29b","number":80,"protocol":"tcp","state":"open","service":{"id":"5d9c8c02777d207de114c29c","name":"http","product":"","version":""}}]}]}';
    return JSON.parse(json).hosts;
}

// convert to ES6
// function getHostsFromServerReal() {
//     // 1) get hosts from API
//     var xhr = new XMLHttpRequest();
//     xhr.open('GET', NMAP_URL + '/nmap/scan/5d9c5742777d207de114c041', true);
//     xhr.onload = function(e){
//         if(xhr.readyState === 4){
//             if(xhr.status === 200){
//                 console.log(xhr.responseText);
//                 // 1) Max house depth
//                 var hosts = JSON.parse(xhr.responseText).hosts;
//                 console.log(hosts);
//             }
//             else {
//                 console.error(xhr.statusText);
//             }
//         }
//         else {
//             // raise exception
//         }
//     };
//     xhr.onerror = function(e){
//         console.error(xhr.statusText);
//     };
//     xhr.send(null);
// }

/**
 * @param hosts
 * @returns {number}
 */
function computeMapSize(hosts) {
    const nbServices = [];
    hosts.forEach(host => {
        let nbServicesHost = 0;
        host.ports.forEach(({service}) => {
            if(typeof service.id !== 'undefined')
                nbServicesHost++;
        });
        nbServices.push(nbServicesHost);
    });
    const maxDepth = Math.max(...nbServices) * 2 + 3;

    // 2) width from maxHouseDepth * 2 + streetwidth
    const width = maxDepth * 2 + STREET_WIDTH;

    // 3) height from ceil(nb hosts / 2) * 4 + 1
    const height = Math.ceil(hosts.length / 2) * 4 + 1;

    // 4) make a square of highest of two values
    return Math.max(width, height);
}

class Controls {
    constructor() {
        this.codes = { 37: 'left', 39: 'right', 38: 'forward', 40: 'backward' };
        this.states = { 'left': false, 'right': false, 'forward': false, 'backward': false };
        document.addEventListener('keydown', this.onKey.bind(this, true), false);
        document.addEventListener('keyup', this.onKey.bind(this, false), false);
    }

    /**
     * @param val
     * @param e
     */
    onKey(val, e) {
        const state = this.codes[e.keyCode];
        if(typeof state === 'undefined')
            return;
        this.states[state] = val;
        e.preventDefault && e.preventDefault();
        e.stopPropagation && e.stopPropagation();
    }
}

/**
 * @param src
 * @param width
 * @param height
 * @constructor
 */
function Bitmap(src, width, height) {
    this.image = new Image();
    this.image.src = src;
    this.width = width;
    this.height = height;
}

// origin of ray casting
class Player {
    /**
     * @param x
     * @param y
     * @param direction
     */
    constructor(x, y, direction) {
        this.x = x;
        this.y = y;
        this.direction = direction;
        this.weapon = new Bitmap('assets/knife_hand.png', 319, 320);
        this.paces = 0;
    }

    /**
     * @param angle
     */
    rotate(angle) {
        this.direction = (this.direction + angle + CIRCLE) % (CIRCLE);
    }

    /*
        dx and dy computed independently to be able to slide along a wall
     */
    /**
     * @param distance
     * @param map
     */
    walk(distance, map) {
        const dx = Math.cos(this.direction) * distance;
        const dy = Math.sin(this.direction) * distance;
        if(map.get(this.x + dx, this.y) <= 0)
            this.x += dx;
        if(map.get(this.x, this.y + dy) <= 0)
            this.y += dy;
        this.paces += distance;
    }

    /**
     * @param left
     * @param right
     * @param forward
     * @param backward
     * @param map
     * @param seconds
     */
    update({left, right, forward, backward}, map, seconds) {
        if(left)
            this.rotate(-Math.PI * seconds);
        if(right)
            this.rotate(Math.PI * seconds);
        if(forward)
            this.walk(3 * seconds, map);
        if(backward)
            this.walk(-3 * seconds, map);
    }
}

// two dimensionnal array
// 0 for no wall, 1 and > for walls
class Map {
    /**
     * @param size
     * @param ports
     */
    constructor(size, ports) {
        this.size = size;
        this.wallGrid = new Uint8Array(size * size);
        this.skybox = new Bitmap('../../public/build/shooter/deathvalley_panorama.jpg', 2000, 750);
        this.wallTexture = new Bitmap('../images/shooter/wall_texture.jpg', 256, 256);
        this.ports = ports;
        this.light = 0;
    }

    /**
     * @param x
     * @param y
     * @returns {number}
     */
    get(x, y) {
        x = Math.floor(x);
        y = Math.floor(y);
        if(x < 0 || x > this.size - 1 || y < 0 || y > this.size - 1)
            return -1;
        return this.wallGrid[y * this.size + x];
    }

    /**
     * @param init
     * @param leftHost
     * @param leftHostDepth
     * @param leftOffset
     * @param rightHost
     * @param rightHostDepth
     * @param rightOffset
     * @param i
     * @param size
     * @param eastSide
     * @returns {*}
     */
    drawFirstLine(
        init,
        leftHost,
        leftHostDepth,
        leftOffset,
        rightHost,
        rightHostDepth,
        rightOffset,
        i,
        size,
        eastSide
    ) {
        // on level scale. i is level nb
        // var init = i * size * HOUSE_HEIGHT;

        init = this.drawAndIncrement(init, leftOffset, 0, 'simple');
        init = this.drawAndIncrement(init, leftHostDepth, 1, 'simple');
        init = this.drawAndIncrement(init, STREET_WIDTH, 0, 'simple');

        if(eastSide){
            init = this.drawAndIncrement(init, rightHostDepth, 1, 'simple');
            init = this.drawAndIncrement(init, rightOffset, 0, 'simple');
        }
        else {
            const remaining = (size - STREET_WIDTH) / 2;
            init = this.drawAndIncrement(init, remaining, 0, 'simple');
        }
        return init;
    }

    /**
     * @param init
     * @param leftHost
     * @param leftOffset
     * @param leftInnerDepth
     * @param rightHost
     * @param rightOffset
     * @param rightInnerDepth
     * @param i
     * @param size
     * @param eastSide
     * @param second
     * @returns {*}
     */
    drawIntermediate(
        init,
        leftHost,
        leftOffset,
        leftInnerDepth,
        rightHost,
        rightOffset,
        rightInnerDepth,
        i,
        size,
        eastSide,
        second = false
    ) {

        // intermediate : offset 0, 1 of 1, depth-2 of 0, 1 of 1 //
        //                 street width 0 //
        //                 1 of 1, depth-2 of 0, 1 of 1, offset 0
        // if(second)
        //     var init = i * size * HOUSE_HEIGHT + 3 * size;
        // else
        //     var init = i * size * HOUSE_HEIGHT + size;

        init = this.drawAndIncrement(init, leftOffset, 0, 'simple');
        init = this.drawAndIncrement(init, 1, 1, 'simple');
        init = this.drawAndIncrement(init, leftInnerDepth, 0, 'simple');
        init = this.drawAndIncrement(init, 1, 1, 'simple');
        init = this.drawAndIncrement(init, STREET_WIDTH, 0, 'simple');

        // eastside ?
        if(eastSide){
            init = this.drawAndIncrement(init, 1, 1, 'simple');
            init = this.drawAndIncrement(init, rightInnerDepth, 0, 'simple');
            init = this.drawAndIncrement(init, 1, 1, 'simple');
            init = this.drawAndIncrement(init, rightOffset, 0, 'simple');
        }
        else {
            const remaining = (size - STREET_WIDTH) / 2;
            init = this.drawAndIncrement(init, remaining, 0, 'simple');
        }
        return init;
    }

    /**
     * @param init
     * @param leftHost
     * @param leftOffset
     * @param leftInnerDepth
     * @param rightHost
     * @param rightOffset
     * @param rightInnerDepth
     * @param i
     * @param size
     * @param eastSide
     * @returns {*}
     */
    drawMiddle(
        init,
        leftHost,
        leftOffset,
        leftInnerDepth,
        rightHost,
        rightOffset,
        rightInnerDepth,
        i,
        size,
        eastSide
    ) {
        // columns : offset 0, 1 of 1
        //             loop until innerDepth
        //                 alternate 0 and 1 (must end w/ 0)
        //             1 of 1
        //             streetwidth 0, 1 of 1, loop, 1 of 1, offset 0
        // var init = i * size * HOUSE_HEIGHT + 2 * size;

        // In alternate draws, differentiate service blocks

        // leftoffset
        init = this.drawAndIncrement(init, leftOffset, 0, 'simple');
        init = this.drawAndIncrement(init, 1, 1, 'simple');
        init = this.drawAndIncrement(init, leftInnerDepth, 0, 'alternate', leftHost);
        init = this.drawAndIncrement(init, 1, 0, 'simple');
        init = this.drawAndIncrement(init, STREET_WIDTH, 0, 'simple');

        if(eastSide){
            init = this.drawAndIncrement(init, 1, 0, 'simple');
            init = this.drawAndIncrement(init, rightInnerDepth, 0, 'alternate', rightHost);
            init = this.drawAndIncrement(init, 1, 1, 'simple');
            init = this.drawAndIncrement(init, rightOffset, 0, 'simple');
        }
        else {
            const remaining = (size - STREET_WIDTH) / 2;
            init = this.drawAndIncrement(init, remaining, 0, 'simple');
        }
        return init;
    }

    /**
     * @param init
     * @param size
     * @param levelNb
     * @returns {*}
     */
    drawLastLine(init, size, levelNb) {
        // var init = HOUSE_HEIGHT * (size - 1) * levelNb;
        init = this.drawAndIncrement(init, size, 1, 'simple');
        return init;
    }

    /**
     * @param init
     * @param drawNb
     * @param filler
     * @param mode
     * @param hostData
     * @returns {*}
     */
    drawAndIncrement(init, drawNb, filler = 0, mode = 'simple', hostData = null) {
        if(mode === 'simple'){
            // also works if drawNb == 1
            for(let j=init; j<init+drawNb; j++)
                this.wallGrid[j] = filler;
            init += drawNb;
        }
        else if(mode === 'alternate' ){
            let odd = false;
            let k = 0;
            for(let j=init; j<init+drawNb; j++) {
                if(odd) {
                    // increment k for each odd

                    // cheap : uint8array, 255 most used services
                    // change later to uint16array to cover every TCP port
                    const portNumber = hostData.ports[k].number;

                    // instead of 1
                    this.wallGrid[j] = portNumber;
                    k++;
                    odd = false;
                }
                else {
                    this.wallGrid[j] = 0;
                    odd = true;
                }
            }
            init += drawNb;
        }
        else if(mode === 'final'){
            for(let j=init; j<init+drawNb; j++)
                this.wallGrid[j] = filler;
        }
        return init;
    }

    /**
     * @param hosts
     * @param size
     */
    buildMap(hosts, size) {
        // compute number of levels
        const levelNb = Math.ceil(hosts.length / 2);

        let init = 0;
        for(let i=0; i<levelNb; i++){
            // first line : offset 0, depth 1, street width 0, depth 1 + offset 0
            const leftHost = hosts[2*i];
            const previousLeftDepth = leftHostDepth;
            var leftHostDepth = numberOfServices(leftHost) * 2 + 3;
            const leftOffset = size / 2 - STREET_WIDTH / 2 - leftHostDepth;
            const leftInnerDepth = leftHostDepth - 2;

            let eastSide = true;
            if(typeof hosts[2*i + 1] !== 'undefined'){
                var rightHost = hosts[2*i + 1];
                var previousRightDepth = rightHostDepth;
                var rightHostDepth = numberOfServices(rightHost) * 2 + 3;
                var rightOffset = size / 2 - STREET_WIDTH / 2 - rightHostDepth;
                var rightInnerDepth = rightHostDepth - 2;
            }
            else {
                eastSide = false;
            }

            let leftValue;
            let rightValue;
            let leftOffVal;
            let rightOffVal;
            if(i === 0) {
                leftValue = leftHostDepth;
                rightValue = rightHostDepth;
                leftOffVal = leftOffset;
                rightOffVal = rightOffset;
            }
            else{
                if(leftHostDepth > previousLeftDepth) {
                    leftValue = leftHostDepth;
                    leftOffVal = size / 2 - STREET_WIDTH / 2 - leftValue;
                }
                if(rightHostDepth > previousRightDepth) {
                    rightValue = rightHostDepth;
                    rightOffVal = size / 2 - STREET_WIDTH / 2 - rightValue;
                }
            }
            init = this.drawFirstLine(init, leftHost, leftValue, leftOffVal, rightHost, rightValue, rightOffVal, i, size, eastSide);

            init = this.drawIntermediate(init, leftHost, leftOffset, leftInnerDepth, rightHost, rightOffset, rightInnerDepth, i, size, eastSide);
            init = this.drawMiddle(init, leftHost, leftOffset, leftInnerDepth, rightHost, rightOffset, rightInnerDepth, i, size, eastSide);
            init = this.drawIntermediate(init, leftHost, leftOffset, leftInnerDepth, rightHost, rightOffset, rightInnerDepth, i, size, eastSide, true);
        }
        this.drawLastLine(init, size, levelNb);
    }

    /**
     * @param x
     * @param y
     * @param angle
     * @param range
     * @returns {[*]|*[]}
     */
    cast({x, y}, angle, range) {
        const self = this;
        const sin = Math.sin(angle);
        const cos = Math.cos(angle);
        const noWall = { length2: Infinity };

        return ray({
            x: x,
            y: y,
            height: 0,
            distance: 0
        });

        /*
            find nearest horizontal and vertical gridlines (stepX, stepY)
            move to the closer and check for wall (inspect)
            repeat until ray is fully traced
         */
        function ray(origin) {
            // debug stepX and stepY
            const stepX = step(sin, cos, origin.x, origin.y);
            const stepY = step(cos, sin, origin.y, origin.x, true);
            const nextStep = stepX.length2 < stepY.length2
                ? inspect(stepX, 1, 0, origin.distance, stepX.y)
                : inspect(stepY, 0, 1, origin.distance, stepY.x);

            if(nextStep.distance > range)
                return [origin];
            return [origin].concat(ray(nextStep));
        }

        function step(rise, run, x, y, inverted) {
            if(run === 0)
                return noWall;
            // finding grid intersection : whole number of x, find matching y by multiplying by line's slope (rise / run)
            // debug dx and dy
            const dx = run > 0 ? Math.floor(x + 1) - x : Math.ceil(x-1) - x;
            const dy = dx * (rise / run);
            return {
                x: inverted ? y + dy : x + dx,
                y: inverted ? x + dx : y + dy,
                length2: dx * dx + dy * dy
            };
        }

        function inspect(step, shiftX, shiftY, distance, offset) {
            const dx = cos < 0 ? shiftX: 0;
            const dy = sin < 0 ? shiftY: 0;
            step.height = self.get(step.x - dx, step.y - dy);
            if(step.height > 1)
                step.height = 1;
            // throw new Error(step.height);
            step.distance = distance + Math.sqrt(step.length2);
            if(shiftX)
                step.shading = cos < 0 ? 2 : 0;
            else
                step.shading = sin < 0 ? 2 : 1;
            step.offset = offset - Math.floor(offset);
            return step;
        }
    }

    /**
     * @param x
     * @param y
     * @param angle
     * @returns {{x: ({length2: number}|{length2: number, x: *, y: *}), y: ({length2: number}|{length2: number, x: *, y: *})}}
     */
    getCoordinates({x, y}, angle) {
        // debug coordinates
        const sin = Math.sin(angle);
        const cos = Math.cos(angle);

        return ray({
            x: x,
            y: y,
            height: 0,
            distance: 0
        });

        function ray({x, y}) {
            const stepX = step(sin, cos, x, y);
            const stepY = step(cos, sin, y, x, true);

            // if(stepX.length2 < stepY.length2){
            //     return inspect(stepX, 1, 0, origin.distance, stepX.y);
            // }
            // else {
            //     return inspect(stepY, 0, 1, origin.distance, stepY.x);
            // }

            return {
                x: stepX,
                y: stepY
            };
        }

        function step(rise, run, x, y, inverted) {
            if(run === 0)
                return noWall;
            const dx = run > 0 ? Math.floor(x + 1) - x : Math.ceil(x-1) - x;
            const dy = dx * (rise / run);
            return {
                x: inverted ? y + dy : x + dx,
                y: inverted ? x + dx : y + dy,
                length2: dx * dx + dy * dy
            };
        }

        // function inspect(step, shiftX, shiftY, distance, offset) {
        //     var dx = cos < 0 ? shiftX: 0;
        //     var dy = sin < 0 ? shiftY: 0;
        //     // step.height = self.get(step.x - dx, step.y - dy);
        //     // return inspected point coordinates
        //     return {
        //         x: step.x - dx,
        //         y: step.y - dy
        //     };
        // }
    }

    /**
     * @param seconds
     */
    update(seconds) {
        if(this.light > 0)
            this.light = Math.max(this.light - 10 * seconds, 0);
        else if (Math.random() * 5 < seconds)
            this.light = 2;
    }
}

/**
 * @param host
 * @returns {number}
 */
function numberOfServices(host){
    let nbServicesHost = 0;
    host.ports.forEach(({service}) => {
        if(typeof service.id !== 'undefined')
            nbServicesHost++;
    });
    return nbServicesHost;
}

class Camera {
    /**
     * @param canvas
     * @param resolution
     * @param focalLength
     */
    constructor(canvas, resolution, focalLength) {
        this.ctx = canvas.getContext('2d');
        this.width = canvas.width = window.innerWidth * 0.5;
        this.height = canvas.height = window.innerHeight * 0.5;
        this.resolution = resolution;
        this.spacing = this.width / resolution;
        this.focalLength = focalLength || 0.8;
        this.range = 14;
        this.lightRange = 5;
        this.scale = (this.width + this.height) / 1200;
    }

    /*
        resolution : how many rays we cast
        focal length : field of view, angle of rays
        range : maximum ray length
     */
    /**
     * @param player
     * @param map
     */
    render(player, map) {
        this.drawSky(player.direction, map.skybox, map.light);
        this.drawColumns(player, map);
        this.drawWeapon(player.weapon, player.paces);
    }

    /**
     * @param direction
     * @param sky
     * @param ambient
     */
    drawSky(direction, sky, ambient) {
        const width = sky.width * (this.height / sky.height) * 2;
        const left = (direction / CIRCLE) * -width;

        this.ctx.save();
        this.ctx.drawImage(sky.image, left, 0, width, this.height);

        if(left < width - this.width)
            this.ctx.drawImage(sky.image, left + width, 0, width, this.height);
        if(ambient > 0){
            this.ctx.fillStyle = '#ffffff';
            this.ctx.globalAlpha = ambient * 0.1;
            this.ctx.fillRect(0, this.height * 0.5, this.width, this.height * 0.5);
        }
        this.ctx.restore();
    }

    /**
     * @param player
     * @param map
     */
    drawColumns(player, map) {
        this.ctx.save();
        for(let column=0; column < this.resolution; column++){
            /*
                angle at which to cast each ray. Angle depends on 3 things
                - direction player is facing
                - focal length of camera
                - which column currently being drawn
             */
            const x = column / this.resolution - 0.5;
            const angle = Math.atan2(x, this.focalLength);
            // notion of map index in one of these calls
            const ray = map.cast(player, player.direction + angle, this.range);

            // var coordinates = map.getCoordinates(player, player.direction + angle);
            // throw new Error(coordinates.x);

            this.drawColumn(column, ray, angle, map);
        }
        this.ctx.restore();
    }

    /**
     * @param image
     * @param width
     * @param height
     * @param paces
     */
    drawWeapon({image, width, height}, paces) {
        const bobX = Math.cos(paces * 2) * this.scale * 6;
        const bobY = Math.sin(paces * 4) * this.scale * 6;
        const left = this.width * 0.66 + bobX;
        const top = this.height * 0.6 + bobY;
        this.ctx.drawImage(image, left, top, width * this.scale, height * this.scale);
    }

    /**
     * @param column
     * @param ray
     * @param angle
     * @param wallTexture
     * @param light
     */
    drawColumn(column, ray, angle, {wallTexture, light}) {
        const ctx = this.ctx;
        const texture = wallTexture;
        const left = Math.floor(column * this.spacing);
        const width = Math.ceil(this.spacing);
        let hit = -1;

        while (++hit < ray.length && ray[hit].height <= 0);

        for(let s = ray.length - 1; s>=0; s--){
            const step = ray[s];
            // var rainDrops = Math.pow(Math.random(), 3) * s;
            // var rain = (rainDrops > 0) && this.project(1, angle, step.distance);

            if(s === hit){
                // texture part to paint computed with remainder of intersection point
                const textureX = Math.floor(texture.width * step.offset);
                const wall = this.project(step.height, angle, step.distance);

                ctx.globalAlpha = 1;
                // throw new Error(wall.height);
                // switch draw following service
                ctx.drawImage(texture.image, textureX, 0, 1, texture.height, left, wall.top, width, wall.height);
                ctx.fillStyle = '#000fff';
                ctx.globalAlpha = Math.max((step.distance + step.shading) / this.lightRange - light, 0);
                ctx.fillRect(left, wall.top, width, wall.height);
            }

            ctx.fillStyle = '#0a2';
            ctx.globalAlpha = 0.15;
            // while (--rainDrops > 0)
            //     ctx.fillRect(left, Math.random() * rain.top, 1, rain.height);
        }
    }

    /**
     * @param height
     * @param angle
     * @param distance
     * @returns {{top: number, height: number}}
     */
    project(height, angle, distance) {
        // cos to correct fisheye effect
        const z = distance * Math.cos(angle);
        const wallHeight = this.height * height / z;
        const bottom = this.height / 2 * (1 + 1 / z);
        return {
            top: bottom - wallHeight,
            height: wallHeight
        };
    }
}

class GameLoop {
    constructor() {
        this.frame = this.frame.bind(this);
        this.lastTime = 0;
        this.callback = () => {};
    }

    /**
     * @param callback
     */
    start(callback) {
        this.callback = callback;
        requestAnimationFrame(this.frame);
    }

    /**
     * @param time
     */
    frame(time) {
        const seconds = (time - this.lastTime) / 1000;
        this.lastTime = time;
        if(seconds < 0.2)
            this.callback(seconds);
        requestAnimationFrame(this.frame);
    }
}

/**
 * @param hosts
 * @returns {*[]}
 */
function getPortsFromHosts(hosts) {
    // custom textures on the go
    ports = [];
    hosts.forEach(host => {
        if(host.ports.length > 0){
            host.ports.forEach(({number}) => {
                ports.push(number);
            });
        }
    });
    return ports;
}

// TODO : eastside, math principles, HTTP call awaited

const display = document.getElementById('display');
const player = new Player(15.3, -1.2, Math.PI * 0.3);

const hosts = getHostsFromServer();
var ports = getPortsFromHosts(hosts);
const size = computeMapSize(hosts);
const map = new Map(size, ports);

// tidy and add port textures to map
map.ports.forEach(port => {
    // dynamic attribute name
    map[`port_${port}_texture`] = 1;
});

// map.addPortsNumbersAndTextures();
const controls = new Controls();
const camera = new Camera(display, 320, 0.8);
const loop = new GameLoop();

map.buildMap(hosts, size);

loop.start(function frame(seconds) {
    map.update(seconds);
    player.update(controls.states, map, seconds);
    camera.render(player, map);
});