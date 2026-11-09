import MondialRelay from './shop';

// Initialisation dès que le DOM est prêt (compatible Turbolinks / Stimulus)
document.addEventListener('DOMContentLoaded', () => {
  new MondialRelay();
});
