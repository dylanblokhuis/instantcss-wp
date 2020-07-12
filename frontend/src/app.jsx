__webpack_public_path__ = window.icss_params.plugins_url + 'frontend/dist/'

import { h, render, createContext } from 'preact';
import { useState } from "preact/hooks";
import styled, { createGlobalStyle } from "styled-components";

import Sidebar from "./sidebar.jsx";
import Theme from "./components/theme.jsx";
import Editor from "./editor.jsx";
import './utilities.css';

const HideWordpressElements = createGlobalStyle`
  #wpwrap, #wpcontent, #wpbody, #wpbody-content, #___icss {
    height: 100%;
  }
  
  #wpbody-content {
    padding-bottom: 0;
  }
  
  #wpfooter {
    display:none;
  }
  
  #wpbody-content > :not(#___icss) {
    display: none !important;
  }
`

const Wrapper = styled.div`
  display: flex;
  height: 100%;
  margin-left: -20px;
`;

const SidebarWrapper = styled.div`
  width: 250px;
  height: 100%;
  background: ${(props) => props.theme.darker};
  color: ${(props) => props.theme.light};
`;

const Main = styled.main`
  flex: 1;
`;

export const AppContext = createContext({
  file: "",
  setFile: () => null
});

function App() {
  const [file, setFile] = useState("");

  return (
    <Theme>
      <AppContext.Provider value={{
        file,
        setFile
      }}>
        <Wrapper>
          <HideWordpressElements />

          <SidebarWrapper>
            <div className="py-2 px-3  font-weight-bold">Files</div>

            <Sidebar />
          </SidebarWrapper>
          <Main>
            <Editor />
          </Main>
        </Wrapper>
      </AppContext.Provider>
    </Theme>
  )
}

render(<App />, document.querySelector('#___icss'))
