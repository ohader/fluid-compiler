// ####################################################################################################################
// TYPO3 Fluid parser rules for LL(0..k) tokenizer
// @author: Oliver Hader <oliver@typo3.org>
// ####################################################################################################################

// @todo
// + aim for LL(0), currently LL(2) - see `parser.lookahead`
//   * node_attr has flaws with LL(0)
// + `{{{ccc}}}` not supported, but possible in Fluid 2.6...
// + `outer: '{inner: \'value\'}'` handling is flaky...
// + ...

%pragma     lexer.unicode                   false
%pragma     parser.lookahead                2

//%skip       space                           \s
%token      text                            ([^\<\>\{\}]+)

// ####################################################################################################################
// {namespace name}
// {namespace name.part*=Vendor\Library}

%token      nsAssign                        {namespace[\h]                                      -> ns
%token      ns:prefix                       [A-Za-z][A-Za-z0-9*.]*                              -> ns_asn
%token      ns_asn:equals                   =
%token      ns_asn:reference                [A-Za-z][A-Za-z0-9_]*(\\[A-Za-z][A-Za-z0-9_]*)*
%token      ns_asn:_curly                   }                                                   -> default

// ####################################################################################################################
// {escaping on}
// {escapingEnabled = false}

%token      escapingAssign1                 {escaping\h*[=\h]\h*                                -> escaping
%token      escapingAssign2                 {escapingEnabled\h*[=\h]\h*                         -> escaping
%token      escaping:bool                   true|false
%token      escaping:switch                 on|off
%token      escaping:_curly                 }                                                   -> default

// ####################################################################################################################
// <node-0 aa bb="bb" cc=""> </node-0>
// <prefix:viewHelper aa bb="bc" cc=""> </prefix:viewHelper>
// @todo: clarify whether what VH prefix and viewHelper can be, e.g. `my-vh:some-thing.he-re`?!

%token      angle_open_                     <(?!/)                                              -> node_
%token      node_:name_vh                   [A-Za-z][A-Za-z0-9]*:([A-Za-z][A-Za-z0-9.-]*)*      -> node_attr
%token      node_:name_tag                  [A-Za-z][A-Za-z0-9-]*                               -> node_attr
%token      node_attr:space                 \s+
%token      node_attr:name_attr             [A-Za-z][A-Za-z0-9-]*(:[A-Za-z][A-Za-z0-9-]*)?
%token      node_attr:equals                =

%token      node_attr:quote_empty           ""
%token      node_attr:quote                 "                                                   -> node_quot
%token      node_quot:curly_                {                                                   -> __inl1_ext
%token      node_quot:value                 [^"]+
%token      node_quot:quote                 "                                                   -> __shift__ * 1

%token      node_attr:_angle_close          />                                                  -> default
%token      node_attr:_angle                (?<!/)>                                             -> default

%token      angle_close_                    </                                                  -> _node
%token      _node:name_vh                   [A-Za-z][A-Za-z0-9]*:[A-Za-z][A-Za-z0-9.-]*
%token      _node:name_tag                  [A-Za-z][A-Za-z0-9-]*
%token      _node:_angle                    >                                                   -> default

// ####################################################################################################################
// {variable}
// {{variable}}                                                                // view-helpers not supported here
// {prefix:viewHelper()}
// {variable -> prefix:viewHelper() -> prefix:viewHelper(attr:variable)}
// {variable -> prefix:viewHelper() -> prefix:viewHelper(attr:'static')}
// {{nested}} is handled in namespace `inline2`
// {f:render(partial:'A', arguments:"{_all}")} arguments tracking

%token      curly_                          {                                                   -> inl1
%token      inl1:curly_                     {                                                   -> inl2
%token      inl1:name_vh                    [A-Za-z][A-Za-z0-9]*:([A-Za-z][A-Za-z0-9.]*)*       -> inl1_vh
%token      inl2:name_var                   [A-Za-z][A-Za-z0-9.]*
%token      inl2:_curly                     }                                                   -> __shift__ * 1
%token      inl1:name_var                   [A-Za-z_][A-Za-z0-9._]*
%token      inl1:question_spc               \s*\?\s*
%token      inl1:colon_spc                  \s*:\s*

%token      inl1:arrow_spc                  \s*->\s*                                            -> inl1_vh

%token      inl1_vh:name_vh                 [A-Za-z][A-Za-z0-9]*:([A-Za-z][A-Za-z0-9.]*)*

%token      inl1_vh:brace_                  \(                                                  -> inl1_vh_arg

%token      inl1_vh_arg:name_arg            [A-Za-z0-9-]+
%token      inl1_vh_arg:colon_spc           \s*:\s*                                             -> inl1_vh_asn

// -------[ Single/Double/Single-Escaped/Double-Escaped handling ]-----------------------------------------------------
%token      inl1_vh_asn:qs                  (?<!\\)'                                            -> inl1_vh_asn_qs
%token      inl1_vh_asn_qs:curly_           {                                                   -> __inl1_ext
%token      inl1_vh_asn_qs:value            (\\'|[^'])+
%token      inl1_vh_asn_qs:qs               (?<!\\)'                                            -> __shift__ * 1

%token      inl1_vh_asn:qs_esc              \\'                                                 -> inl1_vh_asn_qs_esc
%token      inl1_vh_asn_qs_esc:curly_       {                                                   -> __inl1_ext
%token      inl1_vh_asn_qs_esc:value        (?<=\\').+(?=\\')
%token      inl1_vh_asn_qs_esc:qs_esc       \\'                                                 -> __shift__ * 1

%token      inl1_vh_asn:qd                  (?<!\\)"                                            -> inl1_vh_asn_qd
%token      inl1_vh_asn_qd:curly_           {                                                   -> __inl1_ext
%token      inl1_vh_asn_qd:value            (\\"|[^"])+
%token      inl1_vh_asn_qd:qd               "                                                   -> __shift__ * 1

%token      inl1_vh_asn:qd_esc              \\"                                                 -> inl1_vh_asn_qd_esc
%token      inl1_vh_asn_qd_esc:curly_       {                                                   -> __inl1_ext
%token      inl1_vh_asn_qd_esc:value        (?<=\\").+(?=\\")
%token      inl1_vh_asn_qd_esc:qd_esc       "                                                   -> __shift__ * 1

// -------[ Called from external `node_quot`, ``inl1_vh_asn_qs` or `inl1_vh_asn_qd` ]----------------------------------
%token      __inl1_ext:name_arg             [A-Za-z0-9-]+(?=\s*:\s*)                            -> inl1_vh_arg
%token      __inl1_ext:name_var             [A-Za-z_][A-Za-z0-9._]*
%token      __inl1_ext:question_spc         \s*\?\s*
%token      __inl1_ext:colon_spc            \s*:\s*
%token      __inl1_ext:_curly               }                                                   -> __shift__ * 1
%token      inl1_vh_asn:_curly              }                                                   -> __shift__ * 3
// --------------------------------------------------------------------------------------------------------------------

%token      inl1_vh_asn:numeric             [\d]+(\.\d+)?
%token      inl1_vh_asn:name_var            [A-Za-z][A-Za-z0-9.]*
%token      inl1_vh_asn:comma_spc           \s*,\s*                                             -> __shift__ * 1
%token      inl1_vh_asn:_brace              \)                                                  -> __shift__ * 3
%token      inl1_vh_arg:_brace              \)                                                  -> __shift__ * 2

%token      inl1:_curly                     }                                                   -> __shift__ * 1

// ####################################################################################################################
// root

#root:
    item()*

item:
    ns() | escaping() |
    node_() | _node() | inline_wrapped() |
    text()
#text:
    <text>

// ####################################################################################################################
// node enter

// exposed as `#_node_` when self-closing
#node_:
    ::angle_open_:: ( <name_vh> | <name_tag> )
    ( node_attr() )*
    ::space::* ( ::_angle_close:: #_node_ | ::_angle:: )
#ns:
    ::nsAssign:: <prefix> ( ::equals:: <reference> )? ::_curly::
#escaping:
    ( ::escapingAssign1:: | ::escapingAssign2:: ) ( <bool> | <switch> ) ::_curly::
#node_attr:
    ::space:: <name_attr> ( ::equals:: node_attr_quoted() )? #attr
node_attr_quoted:
    <quote_empty> |
    ::quote:: ( <value> | inline_from_node_attr_quoted() #inline_wrapped )* ::quote::

// ####################################################################################################################
// node leave

#_node:
    ::angle_close_:: ( <name_vh> | <name_tag> ) ::_angle::

// ####################################################################################################################
// inline/text related

#inline_wrapped:
    ::curly_::
    ( inline_vh() | inline_variable() ) inline_chained()*
    ::_curly::
#inline_chained:
    ::arrow_spc:: inline_vh() #inline_chained
inline_from_node_attr_quoted:
    ::curly_::
    (
        ( inline_vh() | inline_variable() ) ( ::arrow_spc:: inline_vh() )* |
        inline_vh_arg() ( ::comma_spc:: inline_vh_arg() )*
    )
    ::_curly::
inline_from_inline_vh_quoted:
    ::curly_:: ( inline_variable() | inline_vh_arg() ( ::comma_spc:: inline_vh_arg() )* ) ::_curly:: #inline_wrapped

inline_variable:
    inline_if() ::question_spc:: inline_then()? ::colon_spc:: inline_else() #ternary |
    ::curly_:: inline_variable() ::_curly:: #inline_wrapped |
    <name_var> #variable
#inline_if:
    <name_var> #ternary_if
#inline_then:
    <name_var> #ternary_then
#inline_else:
    <name_var> #ternary_else

#inline_vh:
    <name_vh> ::brace_::
        ( inline_vh_arg() ( ::comma_spc:: inline_vh_arg() )* )?
    ::_brace::
#inline_vh_arg:
     <name_arg> ::colon_spc:: (
        inline_vh_quoted() #arg_quoted
        | inline_vh_quoted_esc() #arg_quoted_esc
        | inline_vh_arg_variable() #arg_variable
        | <numeric> #arg_numeric
    )
inline_vh_quoted:
    ::qs:: ( <value>? | inline_from_inline_vh_quoted() ) ::qs:: |
    ::qd:: ( <value>? | inline_from_inline_vh_quoted() ) ::qd::
inline_vh_quoted_esc:
    ::qs_esc:: <value>? ::qs_esc:: |
    ::qd_esc:: <value>? ::qd_esc::
#inline_vh_arg_variable:
    <name_var> #variable
