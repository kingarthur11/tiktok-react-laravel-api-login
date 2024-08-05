// LoadingPage.js
import React from 'react';
// import './LoadingPage.css';
import styled from 'styled-components';

const LoadingPage = () => {
    return (
        <Wrapper>
            <div className="loading-container">
                <div className="spinner"></div>
                <p className="loading-text">Loading, please wait...</p>
            </div>
        </Wrapper>
    );
};

export default LoadingPage;


const Wrapper = styled.div`
    .loading-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
}

.spinner {
    border: 8px solid #f3f3f3; /* Light grey */
    border-top: 8px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loading-text {
    font-size: 1.2em;
    color: #333;
}
`;