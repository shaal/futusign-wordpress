import { Component, PropTypes } from 'react';
import { TRANSITION2 } from '../../../../strings';

class PlayerTransition2 extends Component {
  constructor(props) {
    super(props);
    this.readyTimeout = null;
    this.stopTimeout = null;
  }
  componentWillReceiveProps(upProps) {
    const {
      currentlyIsPlaying,
      currentlyPlaying,
      nextPlaying,
      setCover,
      setCurrentlyIsPlaying,
      setNextIsReady,
    } = this.props;
    const upNextPlaying = upProps.nextPlaying;
    const upCurrentlyIsPlaying = upProps.currentlyIsPlaying;
    const upCurrentlyPlaying = upProps.currentlyPlaying;
    // GETTING READY TO PLAY
    if (
      nextPlaying !== TRANSITION2 &&
      upNextPlaying === TRANSITION2
    ) {
      this.readyTimeout = window.setTimeout(() => setNextIsReady(true), 0);
    }
    // START PLAYING
    if (
      currentlyPlaying === TRANSITION2 &&
      !currentlyIsPlaying &&
      upCurrentlyIsPlaying
    ) {
      this.stopTimeout = window.setTimeout(() => setCurrentlyIsPlaying(false), 0);
    }
    // STOP SHOWING
    if (
      currentlyPlaying === TRANSITION2 &&
      upCurrentlyPlaying !== TRANSITION2
    ) {
      window.setTimeout(() => setCover(false), 0);
    }
  }
  shouldComponentUpdate() {
    return false;
  }
  componentWillUnmount() {
    window.clearTimeout(this.readyTimeout);
    window.clearTimeout(this.stopTimeout);
  }
  render() {
    return null;
  }
}
PlayerTransition2.propTypes = {
  currentlyIsPlaying: PropTypes.bool.isRequired,
  currentlyPlaying: PropTypes.string,
  nextPlaying: PropTypes.string,
  setCover: PropTypes.func.isRequired,
  setCurrentlyIsPlaying: PropTypes.func.isRequired,
  setNextIsReady: PropTypes.func.isRequired,
};
export default PlayerTransition2;
