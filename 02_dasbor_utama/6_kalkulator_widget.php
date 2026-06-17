<style>
.calc-widget {
    position: fixed;
    bottom: 90px;
    right: 24px;
    width: 300px;
    background: rgba(20, 20, 30, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    box-shadow: var(--shadow-xl);
    z-index: 1000;
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: fadeUp 0.3s var(--ease-out-expo);
}
.calc-widget.show {
    display: flex;
}
.calc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    cursor: grab;
}
.calc-header h4 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}
.calc-header .btn-close {
    background: transparent;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
}
.calc-header .btn-close:hover {
    color: var(--danger);
}
.calc-display {
    padding: 16px;
    text-align: right;
    font-size: 2rem;
    font-family: monospace;
    font-weight: bold;
    color: #fff;
    word-wrap: break-word;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    min-height: 70px;
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
}
.calc-buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: rgba(255, 255, 255, 0.1);
}
.calc-btn {
    background: rgba(30, 30, 40, 0.6);
    border: none;
    padding: 16px;
    font-size: 1.1rem;
    color: #fff;
    cursor: pointer;
    transition: background 0.1s;
}
.calc-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}
.calc-btn:active {
    background: rgba(255, 255, 255, 0.2);
}
.calc-btn.op {
    color: var(--accent-2);
    font-weight: bold;
    background: rgba(30, 30, 40, 0.8);
}
.calc-btn.eq {
    background: var(--accent);
    color: #fff;
    font-weight: bold;
}
.calc-btn.eq:hover {
    background: var(--accent-2);
}
</style>

<div class="calc-widget" id="calcWidget">
    <div class="calc-header" id="calcHeader">
        <h4><i class="fa-solid fa-calculator"></i> Kalkulator</h4>
        <button class="btn-close" onclick="toggleKalkulator()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="calc-display" id="calcDisplay">0</div>
    <div class="calc-buttons">
        <button class="calc-btn op" onclick="calcInput('C')">C</button>
        <button class="calc-btn op" onclick="calcInput('DEL')"><i class="fa-solid fa-delete-left"></i></button>
        <button class="calc-btn op" onclick="calcInput('%')">%</button>
        <button class="calc-btn op" onclick="calcInput('/')">÷</button>
        
        <button class="calc-btn" onclick="calcInput('7')">7</button>
        <button class="calc-btn" onclick="calcInput('8')">8</button>
        <button class="calc-btn" onclick="calcInput('9')">9</button>
        <button class="calc-btn op" onclick="calcInput('*')">×</button>
        
        <button class="calc-btn" onclick="calcInput('4')">4</button>
        <button class="calc-btn" onclick="calcInput('5')">5</button>
        <button class="calc-btn" onclick="calcInput('6')">6</button>
        <button class="calc-btn op" onclick="calcInput('-')">−</button>
        
        <button class="calc-btn" onclick="calcInput('1')">1</button>
        <button class="calc-btn" onclick="calcInput('2')">2</button>
        <button class="calc-btn" onclick="calcInput('3')">3</button>
        <button class="calc-btn op" onclick="calcInput('+')">+</button>
        
        <button class="calc-btn" onclick="calcInput('00')">00</button>
        <button class="calc-btn" onclick="calcInput('0')">0</button>
        <button class="calc-btn" onclick="calcInput('.')">.</button>
        <button class="calc-btn eq" onclick="calcInput('=')">=</button>
    </div>
</div>

<script>
let calcCurrent = '0';
const calcDisplay = document.getElementById('calcDisplay');

function toggleKalkulator() {
    document.getElementById('calcWidget').classList.toggle('show');
}

function calcInput(val) {
    if (val === 'C') {
        calcCurrent = '0';
    } else if (val === 'DEL') {
        calcCurrent = calcCurrent.length > 1 ? calcCurrent.slice(0, -1) : '0';
    } else if (val === '=') {
        try {
            // Replace display ops with JS ops
            let expr = calcCurrent;
            calcCurrent = eval(expr).toString();
        } catch(e) {
            calcCurrent = 'Error';
        }
    } else {
        if (calcCurrent === '0' || calcCurrent === 'Error') {
            if (['/','*','+','-','%'].includes(val)) {
                calcCurrent += val;
            } else {
                calcCurrent = val;
            }
        } else {
            calcCurrent += val;
        }
    }
    calcDisplay.innerText = calcCurrent;
}

// Simple drag logic
const dragItem = document.getElementById("calcWidget");
const dragHandle = document.getElementById("calcHeader");
let active = false;
let currentX, currentY, initialX, initialY;
let xOffset = 0, yOffset = 0;

dragHandle.addEventListener("mousedown", dragStart, false);
document.addEventListener("mouseup", dragEnd, false);
document.addEventListener("mousemove", drag, false);

function dragStart(e) {
    initialX = e.clientX - xOffset;
    initialY = e.clientY - yOffset;
    if (e.target === dragHandle || dragHandle.contains(e.target)) {
        active = true;
    }
}
function dragEnd(e) {
    initialX = currentX;
    initialY = currentY;
    active = false;
}
function drag(e) {
    if (active) {
        e.preventDefault();
        currentX = e.clientX - initialX;
        currentY = e.clientY - initialY;
        xOffset = currentX;
        yOffset = currentY;
        setTranslate(currentX, currentY, dragItem);
    }
}
function setTranslate(xPos, yPos, el) {
    el.style.transform = "translate3d(" + xPos + "px, " + yPos + "px, 0)";
}
</script>
