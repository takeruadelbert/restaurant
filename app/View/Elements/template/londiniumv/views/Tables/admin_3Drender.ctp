<div id="info"><a href="<?= Router::url("/admin/tables", true) ?>" target="_blank" rel="noopener">3D Model Layout Table</a></div>
<div id="container"></div>

<script>

    var table = <?= $encoded_result ?>;

    var camera, scene, renderer;
    var controls;

    var objects = [];
    var targets = {table: []};

    init();
    animate();

    function init() {

        camera = new THREE.PerspectiveCamera(40, window.innerWidth / window.innerHeight, 1, 10000);
        camera.position.z = 3000;

        scene = new THREE.Scene();

        // table

        for (var i = 0; i < table.length; i += 5) {

            var element = document.createElement('div');
            element.className = 'element';
            element.style.backgroundColor = 'rgba(0,127,127,' + (Math.random() * 0.5 + 0.25) + ')';

            var number = document.createElement('div');
            number.className = 'number';
            number.textContent = (i / 5) + 1;
            element.appendChild(number);

            var symbol = document.createElement('div');
            symbol.className = 'symbol';
            symbol.textContent = table[ i ];
            element.appendChild(symbol);

            var details = document.createElement('div');
            details.className = 'details';
            details.innerHTML = table[ i + 1 ] + '<br>' + table[ i + 2 ];
            element.appendChild(details);

            var object = new THREE.CSS3DObject(element);
            object.position.x = Math.random() * 4000 - 2000;
            object.position.y = Math.random() * 4000 - 2000;
            object.position.z = Math.random() * 4000 - 2000;
            scene.add(object);

            objects.push(object);

            //

            var object = new THREE.Object3D();
            object.position.x = (table[ i + 3 ] * 140) - 1330;
            object.position.y = -(table[ i + 4 ] * 180) + 990;

            targets.table.push(object);

        }

        renderer = new THREE.CSS3DRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('container').appendChild(renderer.domElement);

        //

        controls = new THREE.TrackballControls(camera, renderer.domElement);
        controls.rotateSpeed = 0.5;
        controls.minDistance = 500;
        controls.maxDistance = 6000;
        controls.addEventListener('change', render);

        transform(targets.table, 2000);

        //

        window.addEventListener('resize', onWindowResize, false);

    }

    function transform(targets, duration) {

        TWEEN.removeAll();

        for (var i = 0; i < objects.length; i++) {

            var object = objects[ i ];
            var target = targets[ i ];

            new TWEEN.Tween(object.position)
                    .to({x: target.position.x, y: target.position.y, z: target.position.z}, Math.random() * duration + duration)
                    .easing(TWEEN.Easing.Exponential.InOut)
                    .start();

            new TWEEN.Tween(object.rotation)
                    .to({x: target.rotation.x, y: target.rotation.y, z: target.rotation.z}, Math.random() * duration + duration)
                    .easing(TWEEN.Easing.Exponential.InOut)
                    .start();

        }

        new TWEEN.Tween(this)
                .to({}, duration * 2)
                .onUpdate(render)
                .start();

    }

    function onWindowResize() {

        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();

        renderer.setSize(window.innerWidth, window.innerHeight);

        render();

    }

    function animate() {

        requestAnimationFrame(animate);

        TWEEN.update();

        controls.update();

    }

    function render() {

        renderer.render(scene, camera);

    }

</script>