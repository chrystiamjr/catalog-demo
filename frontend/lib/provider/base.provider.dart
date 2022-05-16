import 'dart:async';
import 'package:dio/dio.dart';

class BaseProvider {
  final Dio _client = Dio(BaseOptions(
    baseUrl: 'http://catalog.demo.local', // for local development
    // baseUrl: 'http://localhost:80', // for docker
    contentType: 'application/json',
  ));

  FutureOr<Map<String, dynamic>> fetch({required String url, Map<String, dynamic>? params}) async {
    try {
      Response response = await _client.get(url, queryParameters: params);
      return Map.from(response.data);
    } on DioError catch (e) {
      return e.response?.data ?? {};
    }
  }

  FutureOr<Map<String, dynamic>> post({required String url, Map data = const {}}) async {
    try {
      Response response = await _client.post(url, data: data);
      return Map.from(response.data);
    } on DioError catch (e) {
      return e.response?.data ?? {};
    }
  }
}
