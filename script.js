const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

const gridSize = 20;
let snake = [{ x: 10, y: 10 }];
let food = { x: 15, y: 15 };
let dx = 1;
let dy = 0;

function update() {
    clearCanvas();
    moveSnake();
    drawSnake();
    drawFood();
    checkCollision();
    setTimeout(update, 100);
}

function clearCanvas() {
    ctx.fillStyle = '#000';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
}

function drawSnake() {
    ctx.fillStyle = '#fff';
    snake.forEach(segment => {
        ctx.fillRect(segment.x * gridSize, segment.y * gridSize, gridSize, gridSize);
    });
}

function drawFood() {
    ctx.fillStyle = '#f00';
    ctx.fillRect(food.x * gridSize, food.y * gridSize, gridSize, gridSize);
}

function moveSnake() {
    const head = { x: snake[0].x + dx, y: snake[0].y + dy };
    snake.unshift(head);
    if (head.x === food.x && head.y === food.y) {
        food = {
            x: Math.floor(Math.random() * (canvas.width / gridSize)),
            y: Math.floor(Math.random() * (canvas.height / gridSize))
        };
    } else {
        snake.pop();
    }
}

function checkCollision() {
    const head = snake[0];
    if (head.x < 0 || head.x >= canvas.width / gridSize || head.y < 0 || head.y >= canvas.height / gridSize) {
        snake = [{ x: 10, y: 10 }];
    }
    for (let i = 1; i < snake.length; i++) {
        if (head.x === snake[i].x && head.y === snake[i].y) {
            snake = [{ x: 10, y: 10 }];
        }
    }
}

document.addEventListener('keydown', (event) => {
    switch (event.key) {
        case 'ArrowUp':
            dx = 0;
            dy = -1;
            break;
        case 'ArrowDown':
            dx = 0;
            dy = 1;
            break;
        case 'ArrowLeft':
            dx = -1;
            dy = 0;

            break;
        case 'ArrowRight':
            dx = 1;
            dy = 0;
            break;
    }
});
update();
