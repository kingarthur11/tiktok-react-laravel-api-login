import './App.css';
import {Route, BrowserRouter, Routes} from "react-router-dom";
import SignIn from "./SignIn";
import Login from './Login';
import GoogleCallback from "./GoogleCallback";

function App() {
  return (
      <BrowserRouter>
          <Routes>
              <Route path="/" element={<Login />}></Route>
              <Route path="/callback" element={<GoogleCallback />}></Route>
          </Routes>
      </BrowserRouter>
  );
}

export default App;
