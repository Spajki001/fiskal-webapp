import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  final String baseUrl;

  ApiService(this.baseUrl);

  Future<Map<String, dynamic>> login(String username, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/index.php'),
      body: {
        'username': username,
        'password': password,
        'submit': 'submit',
      },
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Failed to login');
    }
  }

  Future<List<dynamic>> getTopArticles(int month) async {
    final response = await http.get(
      Uri.parse('$baseUrl/get_top_articles.php?month=$month'),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Failed to load top articles');
    }
  }

  // Add more methods to interact with other PHP scripts as needed
}
