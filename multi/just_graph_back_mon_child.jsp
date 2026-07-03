<%-- 
    Document   : just_see_back
    Created on : Jan 10, 2015, 2:05:13 PM
    Author     : 123014
--%>
<%@page import="java.sql.*,java.io.*,java.util.*"%>
<%@page contentType="text/html" pageEncoding="UTF-8"%>




                      <%
             String products = request.getParameter("products");
                              String  sdate = request.getParameter("sdate");
                            String edate = request.getParameter("edate");
String graph= request.getParameter("graph"); 
//out.println(graph);
String url1="bar_graph_mon_back_child.jsp?products="+products+"&sdate="+sdate+"&edate="+edate;     
String url2="line_graph_mon_child.jsp?products="+products+"&sdate="+sdate+"&edate="+edate;     
//String url3="pie_graph_qtr.jsp?products="+products+"&sdate="+sdate+"&edate="+edate;
String url4="3dbar_graph_back_int_mon_child.jsp?products="+products+"&sdate="+sdate+"&edate="+edate; 
String url5="3dbar_graph_back_stack_mon_child.jsp?products="+products+"&sdate="+sdate+"&edate="+edate;                        
if (graph.equalsIgnoreCase("1"))
response.sendRedirect(url1); 
if (graph.equalsIgnoreCase("2"))
response.sendRedirect(url2);

if (graph.equalsIgnoreCase("4"))
response.sendRedirect(url4);  
if (graph.equalsIgnoreCase("5"))
response.sendRedirect(url5);                                     


%>