var notes = {
    "C": "0, 0, 0",
    "B": "10, 163, 218",
    "A#": "151, 224, 111",
    "A": "98, 156, 70",    
    "G#": "215, 91, 245",
    "G": "79, 41, 172",
    "F#": "255, 253, 118",
    "F": "255, 104, 40",
    "E": "126, 40, 11",
    "D#": "247, 163, 191",
    "D": "232, 30, 20",
    "C#": "191, 191, 191"
}

function rgbForNote(note) {
  return notes[note];
}