var errors = 0;


var cardSet;
var board = [];
var rows = 4;
var columns = 5;

var card1Selected;
var card2Selected;
var audio = new Audio('card1.ogg');

window.onload = ()=> {
    shuffleCards();
    startGame();
}

function shuffleCards() {
    cardSet = [];
    
    // Split the QnA pairs into individual cards, but tag them with the same pairId
    dynamicCardList.forEach(pair => {
        cardSet.push({ src: pair.q, pairId: pair.id });
        cardSet.push({ src: pair.a, pairId: pair.id });
    });

    // Shuffle the deck of objects
    for (let i = 0; i < cardSet.length; i++) {
        let j = Math.floor(Math.random() * cardSet.length); 
        let temp = cardSet[i];
        cardSet[i] = cardSet[j];
        cardSet[j] = temp;
    }
}

function startGame() {
    for (let r = 0; r < rows; r++) {
        let row = [];
        for (let c = 0; c < columns; c++) {
            let cardData = cardSet.pop(); // This is now an object {src, pairId}
            row.push(cardData);

            let card = document.createElement("img");
            card.id = r.toString() + "-" + c.toString();
            card.src = cardData.src + ".png";
            card.classList.add("card");
            
            // Attach the pairId directly to the HTML element for easy comparison later
            card.dataset.pairId = cardData.pairId; 
            
            card.addEventListener("click", selectCard);
            card.addEventListener("click", playSound);
            document.getElementById("board").append(card);
        }
        board.push(row);
    }
    setTimeout(hideCards, 1000);
}


function hideCards() {
    for (let r = 0; r < rows; r++){
        for (let c = 0; c < columns; c++){
            let card = document.getElementById(r.toString() + "-" + c.toString());
            card.src="back.png"
        }
    }
}

function selectCard() {
    if (this.src.includes("back")) {
        let coords = this.id.split("-");
        let r = parseInt(coords[0]);
        let c = parseInt(coords[1]);

        if (!card1Selected) {
            card1Selected = this;
            // board[r][c] is now an object, so we access .src
            card1Selected.src = board[r][c].src + ".png"; 
        }
        else if (!card2Selected && this != card1Selected) {
            card2Selected = this;
            card2Selected.src = board[r][c].src + ".png";
            setTimeout(update, 1000);
        }
    }
}

function update() {
    // Compare the hidden dataset pairIds instead of the image source names
    if (card1Selected.dataset.pairId != card2Selected.dataset.pairId) {
        card1Selected.src = "back.png";
        card2Selected.src = "back.png";
        errors += 1;
        document.getElementById("errors").innerText = errors;
    }
    card1Selected = null;
    card2Selected = null;
}

function playSound(){
    audio.play()
}