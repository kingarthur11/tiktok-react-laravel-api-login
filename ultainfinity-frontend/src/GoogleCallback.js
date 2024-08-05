import React, { useState, useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import styled from 'styled-components';
import Countdown from 'react-countdown';
import LoadingPage from './LoadingPage';

function GoogleCallback() {
    const [loading, setLoading] = useState(true);
    const [data, setData] = useState({});
    const [isExpired, setIsExpired] = useState(false);
    const location = useLocation();
    const [countdownDate, setCountdownDate] = useState(Date.now() + 30000);

    useEffect(() => {
        fetch(`http://localhost:8000/api/auth/tiktok/callback${location.search}`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
            .then((response) => response.json())
            .then((data) => {
                setLoading(false);
                setData(data);
            });
    }, [location.search]);

    if (loading) {
        return <DisplayLoading />;
    } else {
        return (
            <Wrapper>
                <TokenBox>
                    <h1>Authentication Token</h1>
                    {!isExpired && data?.otp ? (
                        <>
                            <Token>{data?.otp}</Token>
                            <div id="countdown">
                                <Countdown 
                                    date={countdownDate} 
                                    renderer={renderer} 
                                    onComplete={() => setIsExpired(true)}
                                />
                            </div>
                        </>
                    ) : (
                        <span className="expired">EXPIRED</span>
                    )}
                </TokenBox>
            </Wrapper>
        );
    }
}

// Countdown renderer
const renderer = ({ hours, minutes, seconds, completed }) => {
    if (completed) {
        return null;
    } else {
        return (
            <span className="countdown-timer">
                {String(hours).padStart(2, '0')}:
                {String(minutes).padStart(2, '0')}:
                {String(seconds).padStart(2, '0')}
            </span>
        );
    }
};

function DisplayLoading() {
    return <div><LoadingPage /></div>;
}

export default GoogleCallback;

const Wrapper = styled.div`
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    padding: 0;
    h1 {
        font-size: 1.5em;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }
    #token {
        font-size: 1.2em;
        color: #333;
        margin: 20px 0;
        word-break: break-word;
    }
    .token-box {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
        width: 400px;
    }
    #countdown {
        font-size: 2em;
        color: #e74c3c;
    }
    .countdown-timer {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 5px;
        background: #ecf0f1;
    }
    .expired {
        font-size: 2em;
        color: #e74c3c;
        background: #ecf0f1;
        padding: 10px 20px;
        border-radius: 5px;
    }
`;

const TokenBox = styled.div`
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 30px;
    width: 400px;
    margin: auto;
    text-align: center;
`;

const Token = styled.p`
    font-size: 1.4em;
    font-weight: bold;
    color: #3498db;
    background: #ecf0f1;
    padding: 10px 20px;
    border-radius: 5px;
    border: 2px solid #3498db; 
    word-break: break-word;
    margin: 20px 0;
`;
