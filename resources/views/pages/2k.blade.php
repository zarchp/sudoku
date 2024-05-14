<?php

use function Livewire\Volt\{state};

//

?>

<x-layouts.app>
    @volt
        <div x-data="{
            board: Array(16).fill(''),
            score: 0,
            bestScore: $persist(0),
            isWin: false,
            isContinue: false,
            starters: [2, 2, 2, 2, 2, 2, 2, 4],
            cells: [],
            randomIntFromInterval(min, max) {
                return Math.floor(Math.random() * (max - min + 1) + min);
            },
            startGame() {
                this.resetGame();
                this.newCell(1, 1, 4);
                this.newCell(2, 4, 2);
                this.newCell(2, 3, 2);
                this.newCell(3, 1, 16);
                this.newCell(3, 2, 16);
                this.newCell(3, 3, 32);
                this.newCell(3, 4, 64);
                this.newCell(4, 1, 1024);
                this.newCell(4, 2, 512);
                this.newCell(4, 3, 256);
                this.newCell(4, 4, 128);
            },
            resetGame() {
                this.score = 13280;
                this.cells = [];
                this.isWin = false;
                this.isContinue = false;
            },
            continueGame() {
                this.isWin = false;
                this.isContinue = true;
            },
            newCell(newTop = null, newLeft = null, newValue = null) {
                const countCells = this.cells.length;
                if (countCells === 16) {
                    return;
                }

                let top = newTop ?? this.randomIntFromInterval(1, 4);
                let left = newLeft ?? this.randomIntFromInterval(1, 4);
                let check = this.cells.some((cell) => cell.top === top && cell.left === left);
                while (check) {
                    top = this.randomIntFromInterval(1, 4);
                    left = this.randomIntFromInterval(1, 4);
                    check = this.cells.some((cell) => cell.top === top && cell.left === left);
                }

                this.cells.push(this.getCell(top, left, newValue));
                console.log(this.cells);
            },
            getCell(newTop = null, newLeft = null, newValue = null, animation = 'animate-scale-up', isMerged = false) {
                let value = newValue ?? 2;
                let positionTop = 8 + (newTop - 1) * 78;
                let positionLeft = 8 + (newLeft - 1) * 78;
                let positionClass = 'top-[' + positionTop + 'px] left-[' + positionLeft + 'px]';
                let textSize = value < 100 ? 'text-4xl' : value < 1000 ? 'text-4xl' : value < 1000 ? 'text-3xl' : 'text-2xl';
                let color = this.getColor(value);

                return {
                    value: value,
                    top: newTop,
                    left: newLeft,
                    style: positionClass + ' ' + color + ' ' + textSize + ' ' + animation,
                    isMerged: isMerged,
                    animate: '',
                };
            },
            getColor(value) {
                let colors = {
                    2: 'bg-amber-800',
                    4: 'bg-amber-600',
                    8: 'bg-amber-400',
                    16: 'bg-red-400',
                    32: 'bg-red-600',
                    64: 'bg-blue-800',
                    128: 'bg-blue-400',
                    256: 'bg-lime-400',
                    512: 'bg-lime-800',
                    1024: 'bg-secondary',
                    2048: 'bg-primary',
                    4096: 'bg-indigo-400',
                    8192: 'bg-indigo-600',
                    16384: 'bg-gray-800',
                };

                return colors[value];
            },
            resetIsMerged() {
                let el = document.querySelector('.animate-scale-up');
                console.log(el);
                if (el !== null && el.classList.contains('animate-scale-up')) {
                    el.classList.remove('animate-scale-up');
                    el.classList.remove('animate-pop-out');
                }

                console.log(this.cells);
                for (index = 0; index <= this.cells.length; index++) {
                    if (this.cells[index] !== undefined) {
                        this.cells[index].isMerged = false;
                        if (this.cells[index].value === 2048 && !this.isContinue) {
                            this.isWin = true;
                        }
                        {{-- if (this.cells[index].top === 0) {
                            this.cells.splice(index, 1);
                        } --}}
                    }
                }
            },
            move(direction) {
                if (this.isWin) {
                    return;
                }

                switch (direction) {
                    case 'up':
                        this.moveUp();
                        break;
                    case 'down':
                        this.moveDown();
                        break;
                    case 'left':
                        this.moveLeft();
                        break;
                    case 'right':
                        this.moveRight();
                        break;
                    default:
                        break;
                }
                console.log(this.cells);
                this.resetIsMerged();
                this.newCell();
            },
            moveUp() {
                console.log('up');
                for (row = 2; row <= 4; row++) {
                    for (column = 1; column <= 4; column++) {
                        const currentIndex = this.findCellIndex(row, column);
                        if (currentIndex === -1) {
                            continue;
                        }

                        let maxTop = 2;
                        let increment = -1;

                        const currentCell = this.cells[currentIndex];
                        console.log('currentCell: ' + currentCell.top + ' ' + currentCell.left);
                        this.cells.find((cell, index) => {
                            if (cell.top === row && cell.left === column) {
                                let nextIndex = this.findCellIndex(cell.top + increment, cell.left);
                                let newTop = cell.top;
                                let newLeft = cell.left;

                                while (nextIndex === -1 && newTop >= maxTop) {
                                    newTop = newTop + increment;
                                    console.log('loop');
                                    console.log(newTop);
                                    nextIndex = this.findCellIndex(newTop, newLeft);
                                    console.log(nextIndex);
                                    if (nextIndex !== -1) {
                                        newTop = newTop - increment;
                                    }
                                }

                                if (nextIndex !== -1) {
                                    let currentCell = this.cells[currentIndex];
                                    let nextCell = this.cells[nextIndex];
                                    console.log('yes');

                                    if (nextCell.value === currentCell.value && nextCell.isMerged === false) {
                                        console.log('same');
                                        newTop = newTop + increment;
                                        let mergeValue = nextCell.value + currentCell.value;
                                        this.score += mergeValue;
                                        this.bestScore = this.score > this.bestScore ? this.score : this.bestScore;

                                        this.cells[nextIndex] = this.getCell(newTop, newLeft, mergeValue, 'animate-pop-out', true);
                                        this.cells[currentIndex] = this.getCell(newTop, newLeft, mergeValue, '', true);
                                        setTimeout(() => {
                                            this.cells[currentIndex] = this.getCell(0, 0, 0, 'hidden', false);
                                        }, 200)

                                        return true;
                                    }
                                }

                                this.cells[index] = this.getCell(newTop, newLeft, cell.value);
                                return true;
                            }
                        });
                    }
                }
            },
            moveDown() {
                console.log('down');
                for (row = 3; row >= 1; row--) {
                    for (column = 4; column >= 1; column--) {
                        const currentIndex = this.findCellIndex(row, column);
                        if (currentIndex === -1) {
                            continue;
                        }

                        let maxTop = 3;
                        let increment = 1;

                        const currentCell = this.cells[currentIndex];
                        console.log('currentCell: ' + currentCell.top + ' ' + currentCell.left);
                        this.cells.find((cell, index) => {
                            if (cell.top === row && cell.left === column) {
                                let nextIndex = this.findCellIndex(cell.top + increment, cell.left);
                                let newTop = cell.top;
                                let newLeft = cell.left;

                                while (nextIndex === -1 && newTop <= maxTop) {
                                    newTop = newTop + increment;
                                    console.log('loop');
                                    console.log(newTop);
                                    nextIndex = this.findCellIndex(newTop, newLeft);
                                    console.log(nextIndex);
                                    if (nextIndex !== -1) {
                                        newTop = newTop - increment;
                                    }
                                }

                                if (nextIndex !== -1) {
                                    let currentCell = this.cells[currentIndex];
                                    let nextCell = this.cells[nextIndex];
                                    console.log('yes');

                                    if (nextCell.value === currentCell.value && nextCell.isMerged === false) {
                                        console.log('same');
                                        newTop = newTop + increment;
                                        let mergeValue = nextCell.value + currentCell.value;
                                        this.score += mergeValue;
                                        this.bestScore = this.score > this.bestScore ? this.score : this.bestScore;

                                        this.cells[nextIndex] = this.getCell(newTop, newLeft, mergeValue, 'animate-pop-out', true);
                                        this.cells[currentIndex] = this.getCell(newTop, newLeft, mergeValue, '', true);
                                        setTimeout(() => {
                                            this.cells[currentIndex] = this.getCell(0, 0, 0, 'hidden', false);
                                        }, 200);

                                        return true;
                                    }
                                }

                                this.cells[index] = this.getCell(newTop, newLeft, cell.value);
                                return true;
                            }
                        });
                    }
                }
            },
            moveLeft() {
                console.log('left');
                for (row = 1; row <= 4; row++) {
                    for (column = 2; column <= 4; column++) {
                        const currentIndex = this.findCellIndex(row, column);
                        if (currentIndex === -1) {
                            continue;
                        }

                        let maxLeft = 2;
                        let increment = -1;

                        const currentCell = this.cells[currentIndex];
                        console.log('currentCell: ' + currentCell.top + ' ' + currentCell.left);
                        this.cells.find((cell, index) => {
                            if (cell.top === row && cell.left === column) {
                                let nextIndex = this.findCellIndex(cell.top, cell.left + increment);
                                let newTop = cell.top;
                                let newLeft = cell.left;

                                while (nextIndex === -1 && newLeft >= maxLeft) {
                                    newLeft = newLeft + increment;
                                    nextIndex = this.findCellIndex(newTop, newLeft);
                                    if (nextIndex !== -1) {
                                        newLeft = newLeft - increment;
                                    }
                                }

                                if (nextIndex !== -1) {
                                    let currentCell = this.cells[currentIndex];
                                    let nextCell = this.cells[nextIndex];
                                    console.log('yes');

                                    if (nextCell.value === currentCell.value && nextCell.isMerged === false) {
                                        console.log('same');
                                        newLeft = newLeft + increment;
                                        let mergeValue = nextCell.value + currentCell.value;
                                        this.score += mergeValue;
                                        this.bestScore = this.score > this.bestScore ? this.score : this.bestScore;

                                        this.cells[nextIndex] = this.getCell(newTop, newLeft, mergeValue, 'animate-pop-out', true);
                                        this.cells[currentIndex] = this.getCell(newTop, newLeft, mergeValue, '', true);
                                        setTimeout(() => {
                                            this.cells[currentIndex] = this.getCell(0, 0, 0, 'hidden', false);
                                        }, 200);

                                        return true;
                                    }
                                }

                                this.cells[index] = this.getCell(newTop, newLeft, cell.value);
                                return true;
                            }
                        });
                    }
                }
            },
            moveRight() {
                console.log('right');
                for (row = 4; row >= 1; row--) {
                    for (column = 3; column >= 1; column--) {
                        const currentIndex = this.findCellIndex(row, column);
                        if (currentIndex === -1) {
                            continue;
                        }

                        let maxLeft = 3;
                        let increment = 1;

                        const currentCell = this.cells[currentIndex];
                        console.log('currentCell: ' + currentCell.top + ' ' + currentCell.left);
                        this.cells.find((cell, index) => {
                            if (cell.top === row && cell.left === column) {
                                let nextIndex = this.findCellIndex(cell.top, cell.left + increment);
                                let newTop = cell.top;
                                let newLeft = cell.left;

                                while (nextIndex === -1 && newLeft <= maxLeft) {
                                    newLeft = newLeft + increment;
                                    nextIndex = this.findCellIndex(newTop, newLeft);
                                    if (nextIndex !== -1) {
                                        newLeft = newLeft - increment;
                                    }
                                }

                                if (nextIndex !== -1) {
                                    let currentCell = this.cells[currentIndex];
                                    let nextCell = this.cells[nextIndex];
                                    console.log('yes');

                                    if (nextCell.value === currentCell.value && nextCell.isMerged === false) {
                                        console.log('same');
                                        newLeft = newLeft + increment;
                                        let mergeValue = nextCell.value + currentCell.value;
                                        this.score += mergeValue;
                                        this.bestScore = this.score > this.bestScore ? this.score : this.bestScore;

                                        this.cells[nextIndex] = this.getCell(newTop, newLeft, mergeValue, 'animate-pop-out', true);
                                        this.cells[currentIndex] = this.getCell(newTop, newLeft, mergeValue, '', true);
                                        setTimeout(() => {
                                            this.cells[currentIndex] = this.getCell(0, 0, 0, 'hidden', false);
                                        }, 200);

                                        return true;
                                    }
                                }

                                this.cells[index] = this.getCell(newTop, newLeft, cell.value);
                                return true;
                            }
                        });
                    }
                }
            },
            findCellIndex(top, left) {
                return this.cells.findIndex((cell) => cell.top === top && cell.left === left);
            },
        }" x-init="startGame()">
            <x-header title="2K48" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-theme-toggle class="btn" title="Toggle Theme" darkTheme="night" lightTheme="retro" />
                    <x-button label="" class="" x-on:click="$wire.drawerSettings = true" responsive
                        icon="o-adjustments-horizontal" title="Settings" />
                </x-slot:actions>
            </x-header>

            <div class="container flex flex-col items-center gap-4 mx-auto" x-on:keydown.down.window="move('down')"
                x-swipe:down="move('down')" x-on:keydown.up.window="move('up')" x-swipe:up="move('up')"
                x-on:keydown.left.window="move('left')" x-swipe:left="move('left')"
                x-on:keydown.right.window="move('right')" x-swipe:right="move('right')">
                <div class="flex gap-2">
                    <div></div>
                    <div class="p-1 text-center text-white rounded bg-slate-600 min-w-20">
                        <div class="text-gray-300">Score</div>
                        <div class="text-lg font-bold" x-text="score"></div>
                    </div>
                    <div class="p-1 text-center text-white rounded min-w-20 bg-slate-600">
                        <div class="text-gray-300">Best</div>
                        <div class="text-lg font-bold" x-text="bestScore"></div>
                    </div>
                </div>

                <div class="relative grid grid-cols-4 grid-rows-4 gap-2 p-2 bg-orange-200 rounded w-80 h-80">
                    <template x-for="(cell, index) in board">
                        <div class="w-[70px] h-[70px] rounded bg-amber-100"></div>
                    </template>
                    <template x-for="(cell, index) in cells">
                        <div class="absolute w-[70px] h-[70px] rounded text-white flex justify-center items-center transition-all linear"
                            x-cloak x-bind:class="cell.style" x-text="cell.value">
                        </div>
                    </template>
                    <div class="absolute w-full h-full" x-show="isWin" x-transition x-cloak>
                        <div class="absolute w-full h-full bg-orange-400 rounded opacity-50"></div>
                        <div class="absolute flex flex-col items-center justify-center w-full h-full">
                            <div class="text-5xl font-bold text-white">You win!</div>
                            <div class="flex gap-4 mt-8">
                                <x-button class="text-white btn-secondary btn-sm" label="Continue"
                                    x-on:click="continueGame()" />
                                <x-button class="text-white btn-primary btn-sm" label="New Game" x-on:click="startGame()" />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <x-button class="text-white btn-primary" icon="o-play" label="New Game" x-on:click="startGame()" />
                </div>
            </div>

            {{-- <div class="m-auto w-full h-[calc(100vh-8rem)] justify-center items-center flex flex-col gap-8 px-4"
                x-on:keydown.up.window="move('up')" x-swipe:up="move('up')" x-on:keydown.down.window="move('down')"
                x-swipe:down="move('down')" x-on:keydown.left.window="move('left')" x-swipe:left="move('left')"
                x-on:keydown.right.window="move('right')" x-swipe:right="move('right')">

                <div class="flex justify-end w-full gap-4 px-2">
                    <div></div>
                    <div class="p-2 font-bold text-center text-white rounded bg-slate-600 min-w-24">
                        <div class="text-gray-400">Score</div>
                        <div class="text-xl font-bold" x-text="score"></div>
                    </div>
                    <div class="p-2 font-bold text-center text-white rounded min-w-24 bg-slate-600">
                        <div class="text-gray-400">Best</div>
                        <div class="text-xl font-bold" x-text="bestScore"></div>
                    </div>
                </div>

                <div class="grid grid-cols-4 grid-rows-4 border-2 rounded border-stone-700">
                    <template x-for="(cell, index) in board">
                        <div class="flex items-center justify-center w-20 h-20 border-2 lg:w-20 lg:h-20 border-stone-700">
                            <div class="flex items-center justify-center font-bold text-white rounded-lg w-18 h-18"
                                x-text="cell.value"
                                x-bind:class="{
                                    'bg-amber-800 text-4xl': cell.value == 2,
                                    'bg-amber-600 text-4xl': cell.value == 4,
                                    'bg-amber-400 text-4xl': cell.value == 8,
                                    'bg-red-400 text-4xl': cell.value == 16,
                                    'bg-red-600 text-4xl': cell.value == 32,
                                    'bg-blue-800 text-4xl': cell.value == 64,
                                    'bg-blue-400 text-2xl': cell.value == 128,
                                    'bg-lime-400 text-2xl': cell.value == 256,
                                    'bg-lime-800 text-2xl': cell.value == 512,
                                    'bg-secondary text-xl': cell.value == 1024,
                                    'bg-primary text-xl': cell.value == 2048,
                                    'bg-indigo-400 text-xl': cell.value == 4096,
                                    'bg-indigo-600 text-xl': cell.value == 8192,
                                    'bg-gray-800 text-xl': cell.value == 16384,
                                    'animate-pop-out': cell.animate == 'animate-pop-out',
                                    'animate-scale-up': cell.animate == 'animate-scale-up',
                                    'animate-slide-up': cell.animate == 'animate-slide-up',
                                }">
                            </div>
                        </div>
                    </template>
                </div>

                <div>
                    <x-button class="btn-primary" icon="s-play" label="New Game" x-on:click="startGame()" />
                </div>
            </div> --}}
        </div>
    @endvolt
</x-layouts.app>
