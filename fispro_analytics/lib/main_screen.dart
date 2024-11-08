import 'package:flutter/material.dart';
import 'api_service.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  _MainScreenState createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  final ApiService _apiService =
      ApiService('http://localhost/fispro_analytics');
  List<dynamic> _topArticles = [];

  void _loadTopArticles() async {
    try {
      final articles = await _apiService.getTopArticles(DateTime.now().month);
      setState(() {
        _topArticles = articles;
      });
    } catch (e) {
      print(e);
    }
  }

  @override
  void initState() {
    super.initState();
    _loadTopArticles();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Top Articles'),
      ),
      body: ListView.builder(
        itemCount: _topArticles.length,
        itemBuilder: (context, index) {
          final article = _topArticles[index];
          return ListTile(
            title: Text(article['Naziv_ART']),
            subtitle: Text('Quantity Sold: ${article['QuantitySold']}'),
          );
        },
      ),
    );
  }
}
