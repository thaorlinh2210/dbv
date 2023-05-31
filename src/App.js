import React from "react";
import "./App.css";

const Navbar = () => {
  return (
    <div className="Navbar">
      <div className="Navbar-logo">Nasi Lemak</div>
      <ul className="Navbar-menu">
        <li className="Navbar-menu-item">Home</li>
        <li className="Navbar-menu-item">About</li>
        <li className="Navbar-menu-item">Services</li>
        <li className="Navbar-menu-item">Contact</li>
      </ul>
    </div>
  );
};

function App() {
  return (
    <div className="App">
      <div>
        <header className="App-header">
          <Navbar />
          <h1>Aquis Roster</h1>
          <p>Enjoy your stay.</p>
        </header>
        <main className="App-content">
          <h2>About Me</h2>

          <p>
            Introducing the Roster App, a user-friendly solution for managing
            team rosters. Easily create, organize, and update rosters for your
            sports teams, volunteer groups, or workforce. Stay organized,
            communicate effectively, and ensure smooth scheduling with the
            Roster App. Accessible on multiple devices for convenient roster
            management anytime, anywhere. Streamline your team management and
            optimize productivity with the Roster App.
          </p>

          {/* <iframe
            src={`${process.env.PUBLIC_URL}/php/message.php`}
            title="PHP Page"
          /> */}
          <button onclick="window.location.href='https://w3docs.com';">
            Click Here
          </button>

          <a href="https://w3docs.com">
            djksvnjk
            {/* <button className="App-button">djnf</button> */}
          </a>

          <a href="message.php">
            djksvnjk
            {/* <button className="App-button">djnf</button> */}
          </a>
        </main>
      </div>
      <footer className="App-footer">
        <p>&copy; 2023 My Beautiful Homepage. All rights reserved.</p>
      </footer>
    </div>
  );
}

export default App;
