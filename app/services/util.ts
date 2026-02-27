export const getElementColor = (
    color: 'Neutral' | 'Fire' | 'Water' | 'Earth' | 'Wind' | 'Poison' | 'Holy' | 'Shadow' | 'Undead',
): string => {
    switch (color) {
        case 'Neutral':
            return '#e4dbce';
        case 'Fire':
            return '#ff7026';
        case 'Water':
            return '#79d7fd';
        case 'Earth':
            return '#bb7b2f';
        case 'Wind':
            return '#ffc701';
        case 'Poison':
            return '#8cd470';
        case 'Holy':
            return '#fce9eb';
        case 'Shadow':
            return '#755e94';
        case 'Undead':
            return '#C85AE0';
        default:
            return 'black';
    }
};
