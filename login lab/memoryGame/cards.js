var errors = 0;
var cardList = [
    "joker",
    "mime", 
    "chad",
    "hologram",
    "cavendish",
    "blueprint",
    "perkeo",
    "luckyCat",
    "invis",
    "baseball"
]

var cardSet;
var board = [];
var rows = 4;
var columns = 5;

var card1Selected;
var card2Selected;

window.onload = ()=> {
    shuffleCards();
    startGame();
}

function shuffleCards() {
    cardSet = cardList.concat(cardList); // two of each card
    console.log(cardSet);

    // shuffle
    for (let i = 0; i < cardSet.length; i++) {
        let j = Math.floor(Math.random() * cardSet.length); // get random index
        let temp = cardSet[i];
        cardSet[i] = cardSet[j];
        cardSet[j] = temp;
    }
    console.log(cardSet);
}

function startGame(){
    // arrange board 4x5
    for (let r = 0; r < rows; r++){
        let row = [];
        for (let c = 0; c < columns; c++){
            let cardImg = cardSet.pop();
            row.push(cardImg);

            // <img id= "0-0" class="card" src = "joker.png">
            let card = document.createElement("img");
            card.id = r.toString() + "-" + c.toString();
            card.src = cardImg + ".png";
            card.classList.add("card");
            card.addEventListener("click", selectCard);
            card.addEventListener("click", playSound);
            document.getElementById("board").append(card);
            
        }
        board.push(row);
    }
    console.log(board);
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

function selectCard(){

    if (this.src.includes("back")){
        if (!card1Selected){
            card1Selected = this;

            let coords = card1Selected.id.split("-");
            let r = parseInt(coords[0]);
            let c = parseInt(coords[1]);

            card1Selected.src = board[r][c] + ".png";
        }
        else if (!card2Selected && this != card1Selected){
            card2Selected = this;

            let coords = card2Selected.id.split("-");
            let r = parseInt(coords[0]);
            let c = parseInt(coords[1]);

            card2Selected.src = board[r][c] + ".png";
            setTimeout(update, 1000);
        }
    }
}

function update(){
    // if cards aren't the same, flip both back
    if (card1Selected.src != card2Selected.src){
        card1Selected.src = "back.png";
        card2Selected.src = "back.png";
        errors+= 1;
        document.getElementById("errors").innerText = errors;
    }
    card1Selected = null;
    card2Selected = null;
}

function playSound(){
    audio.play()
}