import { h } from 'preact';
import { ThemeProvider, createGlobalStyle, css } from "styled-components";

export const theme = {
  dark: '#181818',
  darker: '#0f0f0f',
  light: '#fff',
}

const Global = createGlobalStyle`
  body * {
    box-sizing: content-box;
    box-sizing: initial;
    -webkit-font-smoothing: antialiased;
  }
  
  ${props => Object.keys(props.theme).map(key => css`
    .text-${key} {
      color: ${props.theme[key]};
    }
    
    .bg-${key} {
      background: ${props.theme[key]};
    }
  `)}
  
  @keyframes spinner-border {
    to { transform: rotate(360deg); }
  }
`

const Theme = ({ children }) => {
  return (
    <ThemeProvider theme={theme}>
      <Global />
      {children}
    </ThemeProvider>
  );
};

export default Theme;
