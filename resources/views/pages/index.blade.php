<?php

use function Livewire\Volt\{state};

state([
    'myModal2' => false,
]);

?>

<x-layouts.app>
    @volt
        <div x-data="{
            board: [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 5, 6, 7, 8, 9, 1, 2, 3],
                [7, 8, 9, 1, 2, 3, 4, 5, 6],
        
                [2, 3, 1, 5, 6, 4, 8, 9, 7],
                [5, 6, 4, 8, 9, 7, 2, 3, 1],
                [8, 9, 7, 2, 3, 1, 5, 6, 4],
        
                [3, 1, 2, 6, 4, 5, 9, 7, 8],
                [6, 4, 5, 9, 7, 8, 3, 1, 2],
                [9, 7, 8, 3, 1, 2, 6, 4, 5],
            ],
            cells: [],
            activeCell: {},
            difficulties: [{
                'name': 'Beginner',
                'value': 5,
            }, {
                'name': 'Easy',
                'value': 25,
            }, {
                'name': 'Medium',
                'value': 35,
            }, {
                'name': 'Hard',
                'value': 50,
            }, {
                'name': 'Extreme',
                'value': 64,
            }, ],
            selectedDifficulty: $persist('Easy'),
            currentDifficulty: $persist('Easy'),
            blankCell: $persist(10),
            values: ['1', '2', '3', '4', '5', '6', '7', '8', '9', 'X'],
            seconds: 0,
            isRunning: false,
            isWin: false,
            interval: null,
            intervalConfetti: null,
            start() {
                this.isRunning = true;
                this.interval = setInterval(() => {
                    this.seconds++;
                }, 1000);
            },
            stop() {
                this.isRunning = false;
                clearInterval(this.interval);
            },
            reset() {
                this.isRunning = false;
                this.seconds = 0;
                clearInterval(this.interval);
            },
            formatTime() {
                const hours = Math.floor(this.seconds / 3600);
                const minutes = Math.floor((this.seconds % 3600) / 60);
                const seconds = this.seconds % 60;
        
                return hours > 0 ? `${hours}H ${minutes}M ${seconds}S` : minutes > 0 ? `${minutes}M ${seconds}S` : `${seconds}S`;
            },
            pad(number) {
                return (number < 10 ? '0' : '') + number;
            },
            setDifficulty(name) {
                this.selectedDifficulty = name;
                this.blankCell = this.difficulties.find(d => d.name === name).value;
            },
            isObject(obj) {
                return typeof obj === 'object';
            },
            randomInteger(min, max) {
                return Math.floor(Math.random() * (max - min + 1)) + min;
            },
            shuffleNumbers() {
                for (let i = 1; i <= 9; i++) {
                    const randomNumber = this.randomInteger(1, 9);
                    this.swapNumbers(i, randomNumber);
                }
            },
            swapNumbers(n1, randomNumber) {
                for (let y = 0; y < 9; y++) {
                    for (let x = 0; x < 9; x++) {
                        if (this.board[x][y] === n1) {
                            this.board[x][y] = randomNumber;
                        } else if (this.board[x][y] === randomNumber) {
                            this.board[x][y] = n1;
                        }
                    }
                }
            },
            shuffleRows() {
                let blockNumber;
                for (let i = 0; i < 9; i++) {
                    const randomNumber = Math.floor(Math.random() * 3);
                    blockNumber = Math.floor(i / 3);
                    this.swapRows(i, blockNumber * 3 + randomNumber);
                }
            },
            swapRows(r1, r2) {
                const row = this.board[r1];
                this.board[r1] = this.board[r2];
                this.board[r2] = row;
            },
            shuffleCols() {
                let blockNumber;
                for (let i = 0; i < 9; i++) {
                    const randomNumber = Math.floor(Math.random() * 3);
                    blockNumber = Math.floor(i / 3);
                    this.swapCols(i, blockNumber * 3 + randomNumber);
                }
            },
            swapCols(c1, c2) {
                const colVal = [];
                for (let i = 0; i < 9; i++) {
                    colVal[i] = this.board[i][c1];
                    this.board[i][c1] = this.board[i][c2];
                    this.board[i][c2] = colVal[i];
                }
            },
            shuffle3X3Rows() {
                for (let i = 0; i < 3; i++) {
                    const ranNum = Math.floor(Math.random() * 3);
                    this.swap3X3Rows(i, ranNum);
                }
            },
            swap3X3Rows(r1, r2) {
                for (let i = 0; i < 3; i++) {
                    this.swapRows(r1 * 3 + i, r2 * 3 + i);
                }
            },
            shuffle3X3Cols() {
                for (let i = 0; i < 3; i++) {
                    const ranNum = Math.floor(Math.random() * 3);
                    this.swap3X3Cols(i, ranNum);
                }
            },
            swap3X3Cols(c1, c2) {
                for (let i = 0; i < 3; i++) {
                    this.swapCols(c1 * 3 + i, c2 * 3 + i);
                }
            },
            removeCell(total) {
                let i = 0
                while (i < total) {
                    while (true) {
                        let randomRow = this.randomInteger(0, 8);
                        let randomCol = this.randomInteger(0, 8);
                        if (this.board[randomRow][randomCol] !== null) {
                            if (this.isObject(this.board[randomRow][randomCol])) {
                                continue;
                            }
        
                            this.board[randomRow][randomCol] = {
                                row: randomRow,
                                col: randomCol,
                                class: '',
                                value: null,
                                validValue: this.board[randomRow][randomCol],
                            };
                            break;
                        }
                    }
                    i = this.board.flat().filter(v => typeof v === 'object').length;
                }
            },
            setStartingBoard() {
                this.board = [
                    [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    [4, 5, 6, 7, 8, 9, 1, 2, 3],
                    [7, 8, 9, 1, 2, 3, 4, 5, 6],
        
                    [2, 3, 1, 5, 6, 4, 8, 9, 7],
                    [5, 6, 4, 8, 9, 7, 2, 3, 1],
                    [8, 9, 7, 2, 3, 1, 5, 6, 4],
        
                    [3, 1, 2, 6, 4, 5, 9, 7, 8],
                    [6, 4, 5, 9, 7, 8, 3, 1, 2],
                    [9, 7, 8, 3, 1, 2, 6, 4, 5],
                ];
            },
            startGame() {
                document.querySelectorAll('.cell').forEach((cell) => {
                    cell.classList.remove('duration-500');
                    cell.classList.remove('opacity-100');
                    cell.classList.add('opacity-0');
                });
        
                this.stopConfetti();
                this.currentDifficulty = this.selectedDifficulty;
                this.setStartingBoard();
                this.shuffleNumbers();
                this.shuffleRows();
                this.shuffleCols();
                this.shuffle3X3Rows();
                this.shuffle3X3Cols();
                this.removeCell(this.blankCell);
                this.reset();
                this.start();
                this.hideModal();
                this.isWin = false;
        
                setTimeout(() => {
                    {{-- console.log('timeout'); --}}
                    document.querySelectorAll('.cell').forEach((cell) => {
                        setTimeout(() => {
                            cell.classList.add('duration-250');
                            cell.classList.remove('opacity-0');
                            cell.classList.add('opacity-100');
                        }, 250);
                    });
                }, 1);
        
        
        
                {{-- let cells = document.querySelectorAll('.cell'); --}}
        
            },
            startConfetti() {
                var duration = 15 * 1000;
                var animationEnd = Date.now() + duration;
                var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 999 };
        
                function randomInRange(min, max) {
                    return Math.random() * (max - min) + min;
                }
        
                this.intervalConfetti = setInterval(function() {
                    var timeLeft = animationEnd - Date.now();
        
                    if (timeLeft <= 0) {
                        return clearInterval(interval);
                    }
        
                    var particleCount = 50 * (timeLeft / duration);
                    // since particles fall down, start a bit higher than random
                    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } });
                    confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } });
                }, 250);
            },
            stopConfetti() {
                clearInterval(this.intervalConfetti);
            },
            setActiveCell(rowIndex, colIndex) {
                if (this.activeCell.row === rowIndex && this.activeCell.col === colIndex) {
                    this.activeCell = {};
                    return;
                }
        
                this.activeCell = {
                    col: colIndex,
                    row: rowIndex,
                };
        
                {{-- window.startConfetti(); --}}
            },
            setCell(number) {
                if (Object.keys(this.activeCell).length === 0 || this.isWin) {
                    return;
                }
        
                const currentCell = this.board[this.activeCell.row][this.activeCell.col];
                if (!this.isObject(currentCell)) {
                    return;
                }
        
                if (number === 'X') {
                    this.board[this.activeCell.row][this.activeCell.col] = {
                        row: this.activeCell.row,
                        col: this.activeCell.col,
                        class: '',
                        value: null,
                        validValue: currentCell.validValue,
                    };
                }
        
                let allowedNumbers = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
                if (allowedNumbers.includes(number) && Object.keys(this.activeCell).length) {
                    this.board[this.activeCell.row][this.activeCell.col] = {
                        row: this.activeCell.row,
                        col: this.activeCell.col,
                        class: '',
                        value: parseInt(number),
                        validValue: currentCell.validValue,
                    };
                }
        
                this.checkWinner();
            },
            checkWinner() {
                let isFull = this.board.flat().filter(v => v.value).length;
                if (isFull !== this.blankCell) {
                    return;
                }
        
                let correctValues = this.board.flat().filter(v => typeof v === 'object' && v.value === v.validValue).length;
                if (correctValues === this.blankCell) {
                    this.isWin = true;
                    this.stop();
                    setTimeout(() => {
                        this.showModal();
                        this.startConfetti();
                    }, 500)
                }
            },
            showModal() {
                $wire.myModal2 = true;
            },
            hideModal() {
                $wire.myModal2 = false;
            },
            hint() {
                let emptyValues = this.board.flat().filter(v => typeof v === 'object' && v.value === null);
                if (emptyValues.length === 0) {
                    return;
                }
        
                let hintedCell = emptyValues[Math.floor(Math.random() * emptyValues.length)];
                console.log(this.board[hintedCell.row][hintedCell.col]);
                this.board[hintedCell.row][hintedCell.col] = {
                    row: hintedCell.row,
                    col: hintedCell.col,
                    class: '',
                    value: parseInt(hintedCell.validValue),
                    validValue: hintedCell.validValue,
                };
                this.checkWinner();
            },
            validate() {
                let wrongValues = this.board.flat().filter(v => typeof v === 'object' && v.value !== null && v.value !== v.validValue);
                for (let i = 0; i < wrongValues.length; i++) {
                    let currentCell = wrongValues[i];
                    this.board[currentCell.row][currentCell.col] = {
                        row: currentCell.row,
                        col: currentCell.col,
                        class: 'bg-red-400',
                        value: parseInt(currentCell.value),
                        validValue: currentCell.validValue,
                    };
                }
                console.table(wrongValues);
            },
        }" x-init="startGame();">
            <x-header title="Sudoku" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-theme-toggle class="btn" title="Toggle Theme" />
                    <x-button label="" class="" x-on:click="$wire.myModal2 = true" responsive
                        icon="o-adjustments-horizontal" title="Settings" />
                </x-slot:actions>
            </x-header>

            <div class="container flex flex-col items-center gap-0 mx-auto" x-on:keypress.window="setCell($event.key)">
                <div class="flex justify-between w-full max-w-sm lg:max-w-[440px] mb-2">
                    <div>
                        <x-button label="" icon="o-light-bulb" class="btn-warning btn-circle btn-sm"
                            tooltip-right="Hint" x-on:click="hint()" />
                    </div>
                    <div class="text-xl" x-text="formatTime()">00:00</div>
                    <div class="flex gap-2">
                        <x-button label="" icon="s-check" class="btn-primary btn-circle btn-sm"
                            tooltip-right="Validate" x-on:click="validate()" />
                    </div>
                </div>

                <div class="relative grid gap-0 border-4 border-stone-600 grid-rows-9">
                    <template x-for="(rows, rowIndex) in board">
                        <div class="grid grid-cols-9">
                            <template x-for="(col, colIndex) in rows">
                                <div class="relative flex items-center border-stone-600 justify-center w-10 h-10 border-[1px] lg:w-12 lg:h-12 after:opacity-0 after:border-info after:border-4 after:w-full after:h-full after:absolute"
                                    x-bind:data-row="rowIndex" x-bind:data-col="colIndex"
                                    x-on:click="setActiveCell(rowIndex, colIndex)"
                                    x-on:keydown.enter="console.log($event.target.value);"
                                    x-bind:class="{
                                        'after:opacity-100': activeCell.col === colIndex && activeCell.row === rowIndex,
                                        'border-b-4': [2, 5].includes(rowIndex),
                                        'border-r-4': [2, 5].includes(colIndex),
                                        'bg-red-400': col.class,
                                    }">
                                    <div class="flex items-center justify-center w-8 h-8 text-xl transition-all rounded-full opacity-0 duration-250 lg:w-10 lg:h-10 cell"
                                        x-bind:class="{
                                            'bg-base-200': !isObject(col),
                                            {{-- 'opacity-100': setTimeout(() => true, 2000), --}}
                                        }"
                                        x-text="isObject(col) ? col.value : col"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-5 gap-4 mt-4 rounded">
                    <template x-for="(value, index) in values">
                        <div class="flex items-center justify-center w-12 h-12 text-xl rounded-full btn btn-outline"
                            x-text="value" x-on:click="setCell(value)">
                        </div>
                    </template>
                </div>
            </div>

            <x-modal wire:model="myModal2" class="">
                <div>
                    <div class="flex justify-end w-full">
                        <div class="btn-circle btn-ghost btn-outline btn btn-xs" x-on:click="$wire.myModal2 = false">X</div>
                    </div>
                    <div class="flex flex-col gap-2 mb-8" x-show="isWin">
                        <div class="mb-2 text-6xl font-bold text-center text-primary">
                            YOU WIN!
                        </div>
                        <div class="flex justify-center w-full gap-8 text-xl align-middle">
                            <div x-text="currentDifficulty"></div>
                            <div>:</div>
                            <div x-text="formatTime()"></div>
                        </div>
                    </div>
                    <label class="w-full form-control">
                        <div class="label">
                            <span class="label-text">Difficulty</span>
                        </div>
                        <select class="select select-bordered" x-model="selectedDifficulty"
                            x-on:input="setDifficulty($event.target.value)">
                            <template x-for="difficulty in difficulties">
                                <option x-text="difficulty.name" x-bind:selected="difficulty.name === selectedDifficulty">
                                </option>
                            </template>
                        </select>
                    </label>
                    <div class="mt-4 text-center">
                        <x-button label="New Game" class="w-full btn-primary" x-on:click="startGame();" />
                    </div>
                </div>
            </x-modal>
        </div>
    @endvolt
</x-layouts.app>
