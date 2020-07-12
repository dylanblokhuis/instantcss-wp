import { h, render } from 'preact';
import styled, { createGlobalStyle } from "styled-components";

import Sidebar from "./sidebar.jsx";
import './utilities.css';
import Theme from "./components/theme.jsx";

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
  background: green;
`;

function App() {
  return (
    <Theme>
      <Wrapper>
        <HideWordpressElements />

        <SidebarWrapper>
          <div className="py-2 px-3 bg-dark font-weight-bold">Files</div>

          <Sidebar />
        </SidebarWrapper>
        <Main>
          Hey
        </Main>
      </Wrapper>
    </Theme>
  )
}

render(<App />, document.querySelector('#___icss'))
