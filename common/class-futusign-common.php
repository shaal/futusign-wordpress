<?php
/**
 * The common functionality of the plugin.
 *
 * @link       https://github.com/larkintuckerllc
 * @since      0.3.0
 *
 * @package    futusign
 * @subpackage futusign/common
 */
/**
 * The common functionality of the plugin.
 *
 * @package    futusign
 * @subpackage futusign/common
 * @author     John Tucker <john@larkintuckerllc.com>
 */
class Futusign_Common {
	/**
	 * The screen.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      Futusign_Screen    $screen    The screen.
	 */
	private $screen;
	/**
	 * The slide deck.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      Futusign_Slide_Deck    $slide_deck    The slide deck.
	 */
	private $slide_deck;
	/**
	 * The playlist.
	 *
	 * @since    0.3.0
	 * @access   private
	 * @var      Futusign_Playlist    $playlist    The playlist.
	 */
	private $playlist;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.3.0
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->screen = new Futusign_Screen();
		$this->slide_deck = new Futusign_Slide_Deck();
		$this->playlist = new Futusign_Playlist();
	}
	/**
	 * Load the required dependencies for module.
	 *
	 * @since    0.3.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-screen.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-slide-deck.php';
		require_once plugin_dir_path( __FILE__ ) . 'class-futusign-playlist.php';
	}
	/**
	 * Retrieve the screen.
	 *
	 * @since     0.3.0
	 * @return    Futusign_Screen    The screen functionality.
	 */
	public function get_screen() {
		return $this->screen;
	}
	/**
	 * Retrieve the slide deck.
	 *
	 * @since     0.3.0
	 * @return    Futusign_Slide_Deck    The slide deck functionality.
	 */
	public function get_slide_deck() {
		return $this->slide_deck;
	}
	/**
	 * Retrieve the playlist.
	 *
	 * @since     0.3.0
	 * @return    Futusign_Playlist    The playlist functionality.
	 */
	public function get_playlist() {
		return $this->playlist;
	}
}
